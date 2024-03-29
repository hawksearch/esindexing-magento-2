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
     * @param string $value
     * @return $this
     */
    public function setHierarchyId(string $value);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $value
     * @return $this
     */
    public function setName(string $value);

    /**
     * @return string
     */
    public function getParentHierarchyId(): string;

    /**
     * @param string $value
     * @return $this
     */
    public function setParentHierarchyId(string $value);

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsActive(bool $value);

    /**
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setSortOrder(int $value);

    /**
     * @return string
     */
    public function getCustom(): string;

    /**
     * @param string $value
     * @return $this
     */
    public function setCustom(string $value);
}
