<?php
/**
 * Copyright (c) 2024 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Console\Command;

use Magento\AsynchronousOperations\Api\BulkStatusInterface;
use Magento\AsynchronousOperations\Api\Data\BulkSummaryInterface;
use Magento\AsynchronousOperations\Api\Data\BulkSummaryInterfaceFactory;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RetryBulk extends Command
{
    /**#@+
     * Constants for keys of data array
     */
    private const INPUT_BULK_UUID = 'bulk-uuid';
    private const INPUT_STATUSES = 'statuses';
    /**#@-*/

    private const FORBIDDEN_STATUSES = [
        OperationInterface::STATUS_TYPE_COMPLETE
    ];
    private BulkManagementInterface $bulkManagement;
    private BulkStatusInterface $bulkStatus;
    private BulkSummaryInterfaceFactory $bulkSummaryFactory;
    private EntityManager $entityManager;

    /**
     * RetryBulk constructor.
     *
     * @param BulkManagementInterface $bulkManagement
     * @param BulkStatusInterface $bulkStatus
     * @param BulkSummaryInterfaceFactory $bulkSummaryFactory
     * @param EntityManager $entityManager
     * @param string|null $name
     */
    public function __construct(
        BulkManagementInterface $bulkManagement,
        BulkStatusInterface $bulkStatus,
        BulkSummaryInterfaceFactory $bulkSummaryFactory,
        EntityManager $entityManager,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->bulkManagement = $bulkManagement;
        $this->bulkStatus = $bulkStatus;
        $this->bulkSummaryFactory = $bulkSummaryFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName('hawksearch:retry-bulk')
            ->setDescription('Retry hawksearch indexing bulk failed operations')
            ->addArgument(
                self::INPUT_BULK_UUID,
                InputArgument::REQUIRED,
                'Bulk UUID'
            )->addArgument(
                self::INPUT_STATUSES,
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'Space-separated list of bulk operation statuses. Default statuses are 2 and 3 if empty. Status 1 is forbidden for retrial.'
            );
        parent::configure();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bulkUuid = $input->getArgument(self::INPUT_BULK_UUID);
        $statuses = $input->getArgument(self::INPUT_STATUSES);

        try {
            $operations = [];
            if ($statuses) {
                foreach ($statuses as $status) {
                    if (in_array($status, self::FORBIDDEN_STATUSES)) {
                        $output->writeln(__(
                            'Status "%1" is skipped. Operations with such status can\'t be retried.',
                            $status
                        )->render());
                        continue;
                    }
                    $operations = array_merge(
                        $operations,
                        $this->bulkStatus->getFailedOperationsByBulkId($bulkUuid, $status)
                    );
                }
            } else {
                $operations = $this->bulkStatus->getFailedOperationsByBulkId($bulkUuid);
            }


            /** @var BulkSummaryInterface $bulkSummary */
            $bulkSummary = $this->bulkSummaryFactory->create();
            $this->entityManager->load($bulkSummary, $bulkUuid);
            if (!$bulkSummary->getBulkId()) {
                throw new NoSuchEntityException(__('Bulk is not found'));
            }

            if ($this->bulkManagement->scheduleBulk(
                $bulkUuid,
                $operations,
                $bulkSummary->getDescription(),
                $bulkSummary->getUserId()
            )) {
                $output->writeln(__('%1 item(s) have been scheduled for update"', count($operations))->render());
            } else {
                $output->writeln(__('No operations found to retry')->render());
            }
        } catch (\Exception $exception) {
            $phrase = __(
                "An error occurred: %1",
                $exception->getMessage()
            );
            $output->writeln('<error>' . $phrase . '</error>');
            return 1;
        }
        return 0;
    }
}
