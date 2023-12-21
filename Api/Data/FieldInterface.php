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
 * @api v11
 * @see https://developerdocs.hawksearch.com/reference/field_get
 * @see https://developerdocs.hawksearch.com/docs/field-object
 *
 * Since properties in HawkSearch API can be nullable the following argument types in setters
 * should be nullable as well: strings, arrays and objects
 */
interface FieldInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const FIELD_ID = 'FieldId';
    const SYNC_GUID = 'SyncGuid';
    const NAME = 'Name';
    const FIELD_TYPE = 'FieldType';
    const LABEL = 'Label';
    const TYPE = 'Type';
    const BOOST = 'Boost';
    const FACET_HANDLER = 'FacetHandler';
    const IS_PRIMARY_KEY = 'IsPrimaryKey';
    const IS_OUTPUT = 'IsOutput';
    const IS_SHINGLE = 'IsShingle';
    const IS_BEST_FRAGMENT = 'IsBestFragment';
    const IS_DICTIONARY = 'IsDictionary';
    const IS_SORT = 'IsSort';
    const IS_PREFIX = 'IsPrefix';
    const IS_HIDDEN = 'IsHidden';
    const IS_COMPARE = 'IsCompare';
    const SORT_ORDER = 'SortOrder';
    const PARTIAL_QUERY = 'PartialQuery';
    const IS_KEYWORD_TEXT = 'IsKeywordText';
    const IS_QUERY = 'IsQuery';
    const IS_QUERY_TEXT = 'IsQueryText';
    const SKIP_CUSTOM = 'SkipCustom';
    const STRIP_HTML = 'StripHtml';
    const MIN_N_GRAM_ANALYZER = 'MinNGramAnalyzer';
    const MAX_N_GRAM_ANALYZER = 'MaxNGramAnalyzer';
    const COORDINATE_TYPE = 'CoordinateType';
    const OMIT_NORMS = 'OmitNorms';
    const ITEM_MAPPING = 'ItemMapping';
    const DEFAULT_VALUE = 'DefaultValue';
    const USE_FOR_PREDICTION = 'UseForPrediction';
    const COPY_TO = 'CopyTo';
    const ANALYZER = 'Analyzer';
    const DO_NOT_STORE = 'DoNotStore';
    const TAGS = 'Tags';
    const ITERATIONS = 'Iterations';
    const ANALYZER_LANGUAGE = 'AnalyzerLanguage';
    const PREVIEW_MAPPING = 'PreviewMapping';
    const OMIT_TF_ADN_POS = 'OmitTfAndPos';
    const CREATE_DATE = 'CreateDate';
    const MODIFY_DATE = 'ModifyDate';
    const IS_CHILD = 'IsChild';
    const IS_HIERARCHICAL = 'IsHierarchical';
    /**#@-*/

    /**
     * @return int
     */
    public function getFieldId(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setFieldId(int $value): FieldInterface;

    /**
     * @return string
     */
    public function getSyncGuid(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSyncGuid(?string $value): FieldInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setName(?string $value): FieldInterface;

    /**
     * @return string
     */
    public function getFieldType(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setFieldType(?string $value): FieldInterface;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setLabel(?string $value): FieldInterface;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setType(?string $value): FieldInterface;

    /**
     * @return int
     */
    public function getBoost(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setBoost(int $value): FieldInterface;

    /**
     * @return int
     */
    public function getFacetHandler(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setFacetHandler(int $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsPrimaryKey(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsPrimaryKey(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsOutput(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsOutput(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsShingle(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsShingle(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsBestFragment(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsBestFragment(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsDictionary(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsDictionary(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsSort(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsSort(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsPrefix(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsPrefix(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsHidden(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsHidden(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsCompare(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsCompare(bool $value): FieldInterface;

    /**
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setSortOrder(int $value): FieldInterface;

    /**
     * @return string
     */
    public function getPartialQuery(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPartialQuery(?string $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsKeywordText(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsKeywordText(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsQuery(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsQuery(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsQueryText(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsQueryText(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getSkipCustom(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setSkipCustom(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getStripHtml(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setStripHtml(bool $value): FieldInterface;

    /**
     * @return int
     */
    public function getMinNGramAnalyzer(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setMinNGramAnalyzer(int $value): FieldInterface;

    /**
     * @return int
     */
    public function getMaxNGramAnalyzer(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setMaxNGramAnalyzer(int $value): FieldInterface;

    /**
     * @return int
     */
    public function getCoordinateType(): int;

    /**
     * @param int $value
     * @return $this
     */
    public function setCoordinateType(int $value): FieldInterface;

    /**
     * @return bool
     */
    public function getOmitNorms(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setOmitNorms(bool $value): FieldInterface;

    /**
     * @return string
     */
    public function getItemMapping(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setItemMapping(?string $value): FieldInterface;

    /**
     * @return string
     */
    public function getDefaultValue(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setDefaultValue(?string $value): FieldInterface;

    /**
     * @return bool
     */
    public function getUseForPrediction(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setUseForPrediction(bool $value): FieldInterface;

    /**
     * @return string
     */
    public function getCopyTo(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCopyTo(?string $value): FieldInterface;

    /**
     * @return string
     */
    public function getAnalyzer(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setAnalyzer(?string $value): FieldInterface;

    /**
     * @return bool
     */
    public function getDoNotStore(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setDoNotStore(bool $value): FieldInterface;

    /**
     * @return string
     */
    public function getTags(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setTags(?string $value): FieldInterface;

    /**
     * @return int[]
     */
    public function getIterations(): array;

    /**
     * @param int[]|null $value
     * @return $this
     */
    public function setIterations(?array $value): FieldInterface;

    /**
     * @return string
     */
    public function getAnalyzerLanguage(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setAnalyzerLanguage(?string $value): FieldInterface;

    /**
     * @return string
     */
    public function getPreviewMapping(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPreviewMapping(?string $value): FieldInterface;

    /**
     * @return bool
     */
    public function getOmitTfAndPos(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setOmitTfAndPos(bool $value): FieldInterface;

    /**
     * @return string
     */
    public function getCreateDate(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCreateDate(?string $value): FieldInterface;

    /**
     * @return string
     */
    public function getModifyDate(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setModifyDate(?string $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsChild(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsChild(bool $value): FieldInterface;

    /**
     * @return bool
     */
    public function getIsHierarchical(): bool;

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsHierarchical(bool $value): FieldInterface;
}
