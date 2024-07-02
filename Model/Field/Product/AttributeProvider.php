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

use Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal experimental feature
 */
class AttributeProvider
{
    /**
     * @var array
     */
    private array $cachedAttributes = [];

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private ProductAttributeRepositoryInterface $productAttributeRepository;

    /**
     * @var ProductAttributeInterfaceFactory
     */
    private ProductAttributeInterfaceFactory $productAttributeFactory;

    /**
     * @var string
     */
    private string $instanceName;

    /**
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param ProductAttributeInterfaceFactory $productAttributeFactory
     * @param string $instanceName
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        ProductAttributeInterfaceFactory $productAttributeFactory,
        string $instanceName = AttributeAdapter::class
    )
    {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttributeFactory = $productAttributeFactory;
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

            $attribute = $attribute ?? $this->productAttributeFactory->create();

            $this->cachedAttributes[$attributeCode] = ObjectManager::getInstance()->create(
                $this->instanceName,
                ['attribute' => $attribute, 'attributeCode' => $attributeCode]
            );
        }

        return $this->cachedAttributes[$attributeCode];
    }
}
