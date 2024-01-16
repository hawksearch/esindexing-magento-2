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

namespace HawkSearch\EsIndexing\Model\Field\Product;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class AttributeProvider
{
    /**
     * @var array
     */
    private $cachedAttributes = [];

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private ProductAttributeRepositoryInterface $productAttributeRepository;

    /**
     * @var DataObjectFactory
     */
    private DataObjectFactory $dataObjectFactory;

    /**
     * @var string
     */
    private string $instanceName;

    /**
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        DataObjectFactory $dataObjectFactory,
        $instanceName = AttributeAdapter::class
    )
    {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->instanceName = $instanceName;
    }

    /**
     * @param string $attributeCode
     * @return AttributeAdapter
     */
    public function getByCode(string $attributeCode): AttributeAdapter
    {
        if (!isset($this->cachedAttributes[$attributeCode])) {
            try {
                $attribute = $this->productAttributeRepository->get($attributeCode);
            } catch (NoSuchEntityException $e) {
                $attribute = null;
            }

            if (null === $attribute) {
                $attribute = $this->dataObjectFactory->create();
            }

            $this->cachedAttributes[$attributeCode] = ObjectManager::getInstance()->create(
                $this->instanceName,
                ['attribute' => $attribute, 'attributeCode' => $attributeCode]
            );
        }

        return $this->cachedAttributes[$attributeCode];
    }
}
