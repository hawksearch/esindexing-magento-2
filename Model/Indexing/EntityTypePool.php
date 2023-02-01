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

namespace HawkSearch\EsIndexing\Model\Indexing;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;

class EntityTypePool implements EntityTypePoolInterface
{
    /**
     * @var EntityTypeInterface[] | TMap
     */
    private $types;

    /**
     * EntityTypePool constructor.
     * @param TMapFactory $tmapFactory
     * @param array $types
     */
    public function __construct(
        TMapFactory $tmapFactory,
        array $types = []
    ) {
        $this->types = $tmapFactory->createSharedObjectsMap(
            [
                'array' => $types,
                'type' => EntityTypeInterface::class
            ]
        );

        foreach ($this->types as $typeName => $typeInstance) {
            if ($typeInstance->getTypeName() !== $typeName) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Type Name mismatch: %s is expected but %s is given',
                        $typeInstance->getTypeName(),
                        $typeName
                    )
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function get($entityTypeName)
    {
        $types = $this->getList();
        if (isset($types[$entityTypeName])) {
            return $types[$entityTypeName];
        }

        throw new NotFoundException(__('Unknown Entity Type %1', $entityTypeName));
    }

    /**
     * @inheritDoc
     */
    public function getList()
    {
        return $this->types;
    }
}
