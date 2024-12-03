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

/**
 * @internal
 *
 * @template TKey of string
 * @template TValue of EntityTypeInterface
 */
interface EntityTypePoolInterface
{
    /**
     * Gets an entity type instance by its type name
     *
     * @param TKey $entityTypeName
     * @return TValue
     * @throws NotFoundException
     */
    public function get(string $entityTypeName): EntityTypeInterface;

    /**
     * Get a list of entity types
     *
     * @return iterable<TValue>
     */
    public function getList();

    /**
     * Create not shared entity type instance by its type name
     *
     * @param TKey $entityTypeName
     * @return TValue
     * @throws NotFoundException
     */
    public function create(string $entityTypeName): EntityTypeInterface;
}
