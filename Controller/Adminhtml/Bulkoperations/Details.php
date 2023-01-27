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
declare(strict_types=1);

namespace HawkSearch\EsIndexing\Controller\Adminhtml\Bulkoperations;

use HawkSearch\EsIndexing\Model\MessageQueue\BulkAccessValidator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Details extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'HawkSearch_EsIndexing::bulk_operations';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var string
     */
    private $menuId;

    /**
     * @var BulkAccessValidator
     */
    private $bulkAccessValidator;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BulkAccessValidator $bulkAccessValidator
     * @param string $menuId
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BulkAccessValidator $bulkAccessValidator,
        $menuId = 'HawkSearch_EsIndexing::bulk_operations'
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->menuId = $menuId;
        $this->bulkAccessValidator = $bulkAccessValidator;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->bulkAccessValidator->isAllowed($this->getRequest()->getParam('uuid'));
    }

    /**
     * Bulk list action
     *
     * @return Page
     */
    public function execute()
    {
        $bulkId = $this->getRequest()->getParam('uuid');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->initLayout();
        $this->_setActiveMenu($this->menuId);
        $resultPage->getConfig()->getTitle()->prepend(__('Bulk Details - #' . $bulkId));

        return $resultPage;
    }
}
