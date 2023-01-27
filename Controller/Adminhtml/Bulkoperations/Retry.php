<?php
/**
 * Copyright (c) 2023 Hawksearch (www.hawksearch.com) - All Rights Reserved
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
namespace HawkSearch\EsIndexing\Controller\Adminhtml\Bulkoperations;

use HawkSearch\EsIndexing\Model\MessageQueue\BulkAccessValidator;
use Magento\AsynchronousOperations\Api\BulkStatusInterface;
use Magento\AsynchronousOperations\Api\Data\BulkSummaryInterface;
use Magento\AsynchronousOperations\Api\Data\BulkSummaryInterfaceFactory;
use Magento\AsynchronousOperations\Model\BulkNotificationManagement;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Bulk Retry Controller
 */
class Retry extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'HawkSearch_EsIndexing::bulk_operations';

    /**
     * Successful result code.
     */
    private const HTTP_OK = 200;

    /**
     * Internal server error response code.
     */
    private const HTTP_INTERNAL_ERROR = 500;

    /**
     * @var BulkManagementInterface
     */
    private $bulkManagement;

    /**
     * @var BulkNotificationManagement
     */
    private $notificationManagement;

    /**
     * @var BulkAccessValidator
     */
    private $bulkAccessValidator;

    /**
     * @var BulkSummaryInterfaceFactory
     */
    private $bulkSummaryFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var BulkStatusInterface
     */
    private $bulkStatus;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * Retry constructor.
     *
     * @param Context $context
     * @param BulkManagementInterface $bulkManagement
     * @param BulkNotificationManagement $notificationManagement
     * @param BulkAccessValidator $bulkAccessValidator
     * @param BulkSummaryInterfaceFactory $bulkSummaryFactory
     * @param EntityManager $entityManager
     * @param BulkStatusInterface $bulkStatus
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Context $context,
        BulkManagementInterface $bulkManagement,
        BulkNotificationManagement $notificationManagement,
        BulkAccessValidator $bulkAccessValidator,
        BulkSummaryInterfaceFactory $bulkSummaryFactory,
        EntityManager $entityManager,
        BulkStatusInterface $bulkStatus,
        DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context);
        $this->bulkManagement = $bulkManagement;
        $this->notificationManagement = $notificationManagement;
        $this->bulkAccessValidator = $bulkAccessValidator;
        $this->bulkSummaryFactory = $bulkSummaryFactory;
        $this->entityManager = $entityManager;
        $this->bulkStatus = $bulkStatus;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->bulkAccessValidator->isAllowed($this->getRequest()->getParam('uuid'));
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $bulkUuid = $this->getRequest()->getParam('uuid');
        $response = $this->dataObjectFactory->create();
        $response->setError(false);

        $operations = $this->bulkStatus->getFailedOperationsByBulkId($bulkUuid);
        try {
            /** @var BulkSummaryInterface $bulkSummary */
            $bulkSummary = $this->bulkSummaryFactory->create();
            $this->entityManager->load($bulkSummary, $bulkUuid);
            if (!$bulkSummary->getBulkId()) {
                throw new NoSuchEntityException(__('Bulk is not found'));
            }

            if (!$this->bulkManagement->scheduleBulk(
                $bulkUuid,
                $operations,
                $bulkSummary->getDescription(),
                $bulkSummary->getUserId()
            )) {
                throw new NotFoundException(__('No operations found to retry'));
            }
            $responseCode = self::HTTP_OK;
        } catch (NoSuchEntityException|NotFoundException $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
            $responseCode = self::HTTP_INTERNAL_ERROR;
        }

        $this->notificationManagement->ignoreBulks([$bulkUuid]);
        if (!$this->getRequest()->getParam('isAjax')) {
            if (!$response->getError()) {
                $this->messageManager->addSuccessMessage(
                    __('%1 item(s) have been scheduled for update."', count($operations))
                );
            } else {
                $this->messageManager->addErrorMessage($response->getMessage());
            }

            /** @var Redirect $result */
            $result = $this->resultRedirectFactory->create();
            $result->setPath('hawksearch/bulkoperations/index');
        } else {
            /** @var \Magento\Framework\Controller\Result\Json $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $result->setHttpResponseCode($responseCode);
            $result->setData($response);
        }
        return $result;
    }
}
