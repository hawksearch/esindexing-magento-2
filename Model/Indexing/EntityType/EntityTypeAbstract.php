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

namespace HawkSearch\EsIndexing\Model\Indexing\EntityType;

use HawkSearch\EsIndexing\Model\Indexing\AbstractConfigHelper;
use HawkSearch\EsIndexing\Model\Indexing\AttributeHandlerInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityRebuildInterface;
use HawkSearch\EsIndexing\Model\Indexing\EntityTypeInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsDataProviderInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsIndexerInterface;

abstract class EntityTypeAbstract implements EntityTypeInterface
{
    public const ENTITY_TYPE_NAME = null;

    /**
     * @var EntityRebuildInterface
     */
    private $rebuilder;

    /**
     * @var ItemsDataProviderInterface
     */
    private $itemsDataProvider;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @var AttributeHandlerInterface
     */
    private $attributeHandler;

    /**
     * @var ItemsIndexerInterface
     */
    private $itemsIndexer;

    /**
     * @var AbstractConfigHelper
     */
    private $configHelper;

    /**
     * EntityTypeAbstract constructor.
     *
     * @param EntityRebuildInterface $rebuilder
     * @param ItemsDataProviderInterface $itemsDataProvider
     * @param AttributeHandlerInterface $attributeHandler
     * @param ItemsIndexerInterface $itemsIndexer
     * @param AbstractConfigHelper $configHelper
     * @param null $typeName
     */
    public function __construct(
        EntityRebuildInterface $rebuilder,
        ItemsDataProviderInterface $itemsDataProvider,
        AttributeHandlerInterface $attributeHandler,
        ItemsIndexerInterface $itemsIndexer,
        AbstractConfigHelper $configHelper,
        $typeName = null
    ) {
        $this->rebuilder = $rebuilder;
        $this->itemsDataProvider = $itemsDataProvider;
        $this->attributeHandler = $attributeHandler;
        $this->itemsIndexer = $itemsIndexer;
        $this->configHelper = $configHelper;
        $this->typeName = $typeName;
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
    public function setTypeName($type)
    {
        $this->typeName = $type;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRebuilder() : EntityRebuildInterface
    {
        return $this->rebuilder;
    }

    /**
     * @inheritDoc
     */
    public function getItemsDataProvider() : ItemsDataProviderInterface
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
     */
    public function getAttributeHandler() : AttributeHandlerInterface
    {
        return $this->attributeHandler;
    }

    /**
     * @inheritDoc
     */
    public function getConfigHelper(): AbstractConfigHelper
    {
        return $this->configHelper;
    }
}
