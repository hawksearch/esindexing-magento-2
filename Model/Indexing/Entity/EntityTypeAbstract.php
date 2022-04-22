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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity;

use HawkSearch\EsIndexing\Model\Indexing\EntityIndexerInterface;
use HawkSearch\EsIndexing\Model\Indexing\ItemsProviderInterface;

abstract class EntityTypeAbstract implements EntityTypeInterface
{
    public const ENTITY_TYPE_NAME = null;

    /**
     * @var EntityIndexerInterface
     */
    private $entityIndexer;

    /**
     * @var ItemsProviderInterface
     */
    private $itemsProvider;

    /**
     * @var string
     */
    private $typeName;

    /**
     * EntityTypeAbstract constructor.
     * @param EntityIndexerInterface $entityIndexer
     * @param ItemsProviderInterface $itemsProvider
     */
    public function __construct(
        EntityIndexerInterface $entityIndexer,
        ItemsProviderInterface $itemsProvider
    ) {
        $this->entityIndexer = $entityIndexer;
        $this->itemsProvider = $itemsProvider;
        $this->typeName = static::ENTITY_TYPE_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getTypeName()
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
    public function getEntityIndexer()
    {
        return $this->entityIndexer;
    }

    /**
     * @inheritDoc
     */
    public function getItemsProvider()
    {
        return $this->itemsProvider;
    }
}
