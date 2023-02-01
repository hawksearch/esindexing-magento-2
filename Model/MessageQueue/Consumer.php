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
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
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
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * InitFullReindex constructor.
     *
     * @param SerializerInterface $serializer
     * @param ObjectFactory $objectFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        SerializerInterface $serializer,
        ObjectFactory $objectFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->serializer = $serializer;
        $this->objectFactory = $objectFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Process
     * @param QueueOperationDataInterface $operation
     * @return void
     */
    public function process(QueueOperationDataInterface $operation)
    {
        $data = $this->serializer->unserialize($operation->getData());
        $dataObject = $this->objectFactory->create(DataObject::class, ['data' => $data]);

        $class = $dataObject->getClass();
        $method = $dataObject->getMethod();

        if (!$class) {
            return;
        }

        $object = $this->objectFactory->create($class, []);

        if (is_callable([$object, $method])) {
            call_user_func_array([$object, $method], $this->buildArguments($dataObject));
        }
    }

    /**
     * @param DataObject $data
     * @return array
     */
    private function buildArguments(DataObject $data)
    {
        $arguments = $data->getData('method_arguments') ?? [];
        foreach ($arguments as $paramName => $value) {
            if ($paramName == 'searchCriteria') {
                $arguments[$paramName] = $this->buildSearchCriteriaArgument((array)$value);
            }
        }

        return $arguments;
    }

    /**
     * @param array $data
     * @return SearchCriteria
     */
    private function buildSearchCriteriaArgument(array $data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case SearchCriteria::PAGE_SIZE:
                    $this->searchCriteriaBuilder->setPageSize($value);
                    break;
                case SearchCriteria::CURRENT_PAGE:
                    $this->searchCriteriaBuilder->setCurrentPage($value);
                    break;
                case SearchCriteria::SORT_ORDERS:
                    $this->searchCriteriaBuilder->setSortOrders($value);
                    break;
                default:
                    $this->searchCriteriaBuilder->addFilter($key, $value);
            }
        }
        return $this->searchCriteriaBuilder->create();
    }

}
