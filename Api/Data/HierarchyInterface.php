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

namespace HawkSearch\EsIndexing\Api\Data;

/**
 * @api
 * @since 0.8.0
 * @see https://developerdocs.hawksearch.com/reference/hierarchy_upsert-1
 * @see https://indexing-dev.hawksearch.net/swagger/ui/index#!/Hierarchy/Hierarchy_Upsert
 * @todo interface is not used yet. Use it in Indexing API requests/responses
 *
 * Since properties in HawkSearch API can be nullable the following argument types in setters
 * should be nullable as well: strings, arrays and objects
 */
interface HierarchyInterface
{
    /**#@+
     * Constants for keys of data array
     */
    public const FIELD_HIERARCHY_ID = "HierarchyId";
    public const FIELD_NAME = "Name";
    public const FIELD_PARENT_HIERARCHY_ID = "ParentHierarchyId";
    public const FIELD_IS_ACTIVE = "IsActive";
    public const FIELD_SORTORDER = "SortOrder";
    public const FIELD_CUSTOM = "Custom";
    /**#@-*/

    /**
     * @return string
     */
    public function getHierarchyId(): string;

    /**
     * @return $this
     */
    public function setHierarchyId(string $value);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return $this
     */
    public function setName(string $value);

    /**
     * @return string
     */
    public function getParentHierarchyId(): string;

    /**
     * @return $this
     */
    public function setParentHierarchyId(string $value);

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @return $this
     */
    public function setIsActive(bool $value);

    /**
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * @return $this
     */
    public function setSortOrder(int $value);

    /**
     * @return string
     */
    public function getCustom(): string;

    /**
     * @return $this
     */
    public function setCustom(string $value);
}
