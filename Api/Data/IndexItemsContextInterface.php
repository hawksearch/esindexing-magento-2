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

namespace HawkSearch\EsIndexing\Api\Data;

/**
 * IndexItemsContext interface is used in index-items method of Indexing API
 *
 * @internal interface is not used yet
 * @link https://developerdocs.hawksearch.com/reference/indexv2_index-1
 * @link https://indexing-dev.hawksearch.net/swagger/ui/index#!/IndexV2/IndexV2_Index
 */
interface IndexItemsContextInterface
{
    public const FIELD_INDEX_NAME = 'IndexName';
    public const FIELD_ITEMS = 'Items';

    /**
     * @return string
     */
    public function getIndexName(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setIndexName(?string $value);

    /**
     * @return IndexItemInterface[]
     */
    public function getItems(): array;

    /**
     * @param IndexItemInterface[]|null $value
     * @return $this
     */
    public function setItems(?array $value);
}
