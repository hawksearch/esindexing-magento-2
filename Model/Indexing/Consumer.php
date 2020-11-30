<?php
/**
 * Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model\Indexing;

use HawkSearch\EsIndexing\Model\Indexing\IndexManagementInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Consumer
{
    /**
     * @var IndexManagementInterface
     */
    private $indexManagement;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * InitFullReindex constructor.
     * @param IndexManagementInterface $indexManagement
     * @param SerializerInterface $serializer
     */
    public function __construct(
        IndexManagementInterface $indexManagement,
        SerializerInterface $serializer
    ) {
        $this->indexManagement = $indexManagement;
        $this->serializer = $serializer;
    }

    /**
     * Process
     *
     * @param OperationInterface $operation
     *
     * @return void
     */
    public function process(OperationInterface $operation)
    {
        try {
            $serializedData = $operation->getSerializedData();
            $data = $this->serializer->unserialize($serializedData);
            $this->execute($data);
        } catch (\Exception $e) {

        }
    }

    /**
     * Execute
     *
     * @param array $data
     *
     * @return void
     */
    private function execute($data): void
    {

    }
}
