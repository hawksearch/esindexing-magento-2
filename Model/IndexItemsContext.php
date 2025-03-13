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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\EsIndexing\Api\Data\IndexItemInterface;
use HawkSearch\EsIndexing\Api\Data\IndexItemsContextInterface;
use HawkSearch\EsIndexing\Helper\ObjectHelper;
use Magento\Framework\Api\AbstractSimpleObject;

class IndexItemsContext extends AbstractSimpleObject implements IndexItemsContextInterface
{
    /**
     * @param array<self::FIELD_*, mixed> $data
     */
    public function __construct(array $data = [])
    {
        //apply defaults
        $data = $data + [
                self::FIELD_ITEMS => []
            ];
        parent::__construct($data);

        //Validate and reset data for array of objects
        $this->setItems($data[self::FIELD_ITEMS]);
    }

    public function getIndexName(): string
    {
        return (string)$this->_get(self::FIELD_INDEX_NAME);
    }

    public function setIndexName(?string $value): IndexItemsContextInterface
    {
        return $this->setData(self::FIELD_INDEX_NAME, $value);
    }

    public function getItems(): array
    {
        return $this->_get(self::FIELD_ITEMS);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setItems(?array $value): IndexItemsContextInterface
    {
        $value = $value ?? [];
        ObjectHelper::validateListOfObjects($value, IndexItemInterface::class);

        return $this->setData(self::FIELD_ITEMS, $value);
    }
}
