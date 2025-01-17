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

namespace HawkSearch\EsIndexing\Model\Indexing\EntityType;

use HawkSearch\Connector\Compatibility\ParameterDeprecation;
use HawkSearch\Connector\Compatibility\PublicMethodDeprecationTrait;
use HawkSearch\EsIndexing\Model\Indexing\AbstractConfigHelper;
use HawkSearch\EsIndexing\Model\Indexing\EntityRebuildInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityTypeInterface;
use HawkSearch\EsIndexing\Model\Indexing\Field\NameProviderInterface as FieldNameProviderInterface;
use HawkSearch\EsIndexing\Model\Indexing\FieldHandlerInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsDataProviderInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsIndexerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;

abstract class EntityTypeAbstract implements EntityTypeInterface
{
    use PublicMethodDeprecationTrait;

    private array $deprecatedMethods = [
        'getAttributeHandler' => [
            'since' => '0.7.0',
            'replacement' => __CLASS__ . '::getFieldHandler()',
            'description' => 'In favour of a new Field Handlers logic'
        ],
    ];

    /**
     * @var EntityRebuildInterface
     */
    private EntityRebuildInterface $rebuilder;

    /**
     * @var ItemsDataProviderInterface
     */
    private ItemsDataProviderInterface $itemsDataProvider;

    /**
     * @var FieldHandlerInterface<DataObject>
     */
    private FieldHandlerInterface $fieldHandler;

    /**
     * @var ItemsIndexerInterface
     */
    private ItemsIndexerInterface $itemsIndexer;

    /**
     * @var AbstractConfigHelper
     */
    private AbstractConfigHelper $configHelper;

    /**
     * @var string
     */
    private string $typeName;

    /**
     * @var FieldNameProviderInterface
     */
    private FieldNameProviderInterface $fieldNameProvider;

    /**
     * @param EntityRebuildInterface $rebuilder
     * @param ItemsDataProviderInterface $itemsDataProvider
     * @param FieldHandlerInterface<DataObject> $fieldHandler
     * @param ItemsIndexerInterface $itemsIndexer
     * @param AbstractConfigHelper $configHelper
     * @param null $typeName
     * @param FieldNameProviderInterface|null $fieldNameProvider
     * @phpstan-ignore-next-line for $attributeHandler argument
     */
    public function __construct(
        EntityRebuildInterface $rebuilder,
        ItemsDataProviderInterface $itemsDataProvider,
        FieldHandlerInterface $fieldHandler,
        ItemsIndexerInterface $itemsIndexer,
        AbstractConfigHelper $configHelper,
        //@todo deprecate $typeName default value
        string $typeName = null,
        FieldNameProviderInterface $fieldNameProvider = null,
        /**
         * @deprecated 0.7.0 in favour of a new Field Handlers logic.
         * @see $fieldHandler
         * Update dependencies in di.xml file.
         * @phpstan-ignore-next-line
         */
        FieldHandlerInterface $attributeHandler = null
    )
    {
        $this->rebuilder = $rebuilder;
        $this->itemsDataProvider = $itemsDataProvider;
        $this->fieldHandler = $fieldHandler;
        if ($attributeHandler !== null) {
            ParameterDeprecation::triggerDeprecationMessage(
                __METHOD__,
                '$attributeHandler',
                '0.7.0',
                '$fieldHandler',
                'Update dependencies in di.xml file.'
            );
            if ($attributeHandler instanceof FieldHandlerInterface) {
                $this->fieldHandler = $attributeHandler;
            } else {
                throw new \InvalidArgumentException(
                    __(
                        'Argument %1 passed to %2 should be an instance of %3 but %4 is given',
                        '$attributeHandler',
                        __METHOD__,
                        FieldHandlerInterface::class,
                        get_class($attributeHandler)
                    )->render()
                );
            }
        }
        $this->itemsIndexer = $itemsIndexer;
        $this->configHelper = $configHelper;
        /** @todo Avoid setting empty string. Throw InvalidArgumentException */
        $this->typeName = $typeName ?? '';
        $this->fieldNameProvider = $fieldNameProvider ?? ObjectManager::getInstance()->get(FieldNameProviderInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * @inheritDoc
     */
    public function setTypeName(string $type)
    {
        $this->typeName = $type;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUniqueId(string $itemId)
    {
        return $this->getTypeName() . '_' . $itemId;
    }

    /**
     * @inheritDoc
     */
    public function getRebuilder(): EntityRebuildInterface
    {
        return $this->rebuilder;
    }

    /**
     * @inheritDoc
     */
    public function getItemsDataProvider(): ItemsDataProviderInterface
    {
        return $this->itemsDataProvider;
    }

    /**
     * @inheritDoc
     */
    public function getItemsIndexer(): ItemsIndexerInterface
    {
        return $this->itemsIndexer;
    }

    /**
     * @inheritDoc
     * @deprecated 0.7.0 In favour of a new Field Handlers logic
     * @see self::getFieldHandler()
     * @phpstan-ignore-next-line
     */
    public function getAttributeHandler(): FieldHandlerInterface
    {
        $this->triggerPublicMethodDeprecationMessage(__FUNCTION__);
        return $this->getFieldHandler();
    }

    /**
     * @return FieldHandlerInterface<DataObject>
     */
    public function getFieldHandler(): FieldHandlerInterface
    {
        return $this->fieldHandler;
    }

    /**
     * @inheritDoc
     */
    public function getConfigHelper(): AbstractConfigHelper
    {
        return $this->configHelper;
    }

    /**
     * @inheritDoc
     */
    public function getFieldNameProvider(): FieldNameProviderInterface
    {
        return $this->fieldNameProvider;
    }
}
