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

namespace HawkSearch\EsIndexing\Model\MessageQueue;

use HawkSearch\EsIndexing\Api\Data\QueueOperationDataInterface;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use Magento\Framework\Data\ObjectFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * @api
 * @since 0.8.0
 */
class Consumer
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var ObjectFactory
     */
    private ObjectFactory $objectFactory;

    /**
     * @var ObjectHelper
     */
    private ObjectHelper $objectHelper;

    public function __construct(
        SerializerInterface $serializer,
        ObjectFactory $objectFactory,
        ObjectHelper $objectHelper
    )
    {
        $this->serializer = $serializer;
        $this->objectFactory = $objectFactory;
        $this->objectHelper = $objectHelper;
    }

    /**
     * @return string
     */
    public function process(QueueOperationDataInterface $operation)
    {
        $data = $this->serializer->unserialize($operation->getData());
        $dataObject = $this->objectFactory->create(DataObject::class, ['data' => $data]);

        $class = $dataObject->getClass();
        $method = $dataObject->getMethod();

        if (!$class) {
            return '';
        }

        $object = $this->objectFactory->create($class, []);

        if (is_callable([$object, $method])) {
            call_user_func_array([$object, $method], $this->buildArguments($dataObject));
        }

        return $operation->getData();
    }

    /**
     * @return array
     */
    private function buildArguments(DataObject $data)
    {
        $arguments = $data->getData('method_arguments') ?? [];
        foreach ($arguments as $paramName => $value) {
            if ($paramName == 'searchCriteria') {
                $arguments[$paramName] = $this->objectHelper->convertArrayToSearchCriteriaObject((array)$value);
            }
        }

        return $arguments;
    }
}
