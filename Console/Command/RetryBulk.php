<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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
use Magento\Framework\Bulk\BulkManagementInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RetryBulk extends Command
{
    /**
     * @var BulkManagementInterface
     */
    private $bulkManagement;

    /**
     * @var BulkStatusInterface
     */
    private $bulkStatus;

    /**
     * RetryBulk constructor.
     * @param BulkManagementInterface $bulkManagement
     * @param BulkStatusInterface $bulkStatus
     * @param string|null $name
     */
    public function __construct(
        BulkManagementInterface $bulkManagement,
        BulkStatusInterface $bulkStatus,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->bulkManagement = $bulkManagement;
        $this->bulkStatus = $bulkStatus;
    }

    /**
     * @inheritDoc
     */
    protected function configure() : void
    {
        $this->setName('hawksearch:retry-bulk')
            ->setDescription('Retry hawksearch indexing bulk failed operations')
            ->addArgument('bulk-uuid', InputArgument::REQUIRED, 'Bulk UUID');
        parent::configure();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bulkUuid = $input->getArgument('bulk-uuid');
        try {
            $operations = $this->bulkStatus->getFailedOperationsByBulkId($bulkUuid);

            if ($this->bulkManagement->scheduleBulk($bulkUuid, $operations, 'Reschedule bulk')) {
                $output->writeln(__('%1 item(s) have been scheduled for update."', count($operations)));
            } else {
                $output->writeln(__('No failed operations found.'));
            }
        } catch (\Exception $exception) {
            $output->writeln(__('An error occurred: %1', $exception->getMessage()));
            return 1;
        }
        return 0;
    }
}
