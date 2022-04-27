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

use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterface;
use Magento\Framework\Data\ObjectFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;

class Consumer
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * InitFullReindex constructor.
     * @param SerializerInterface $serializer
     * @param ObjectFactory $objectFactory
     */
    public function __construct(
        SerializerInterface $serializer,
        ObjectFactory $objectFactory
    ) {
        $this->serializer = $serializer;
        $this->objectFactory = $objectFactory;
    }

    /**
     * Process
     *
     * @param QueueOperationDataInterface $operation
     *
     * @return void
     */
    public function process(QueueOperationDataInterface $operation)
    {
        $data = $this->serializer->unserialize($operation->getData());
        $dataObject = $this->objectFactory->create(DataObject::class, ['data' => $data]);

        $class = $dataObject->getClass();
        $method = $dataObject->getMethod();
        $arguments = $dataObject->getData('method_arguments') ?? [];

        if (!$class) {
            return;
        }

        $object = $this->objectFactory->create($class, []);

        if (is_callable([$object, $method])) {
            call_user_func_array([$object, $method], $arguments);
        }
    }
}
