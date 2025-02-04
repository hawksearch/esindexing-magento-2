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

namespace HawkSearch\EsIndexing\Api\Data;

/**
 * @api v11
 * @since 0.8.0
 * @link https://developerdocs.hawksearch.com/reference/field_post_value
 * @link https://dev.hawksearch.net/swagger/ui/index#!/Field/Field_Post_value
 *
 * Since properties in HawkSearch API can be nullable the following argument types in setters
 * should be nullable as well: strings, arrays and objects
 */
interface FieldInterface
{
    const FIELD_ID = 'FieldId';
    const SYNC_GUID = 'SyncGuid';
    const LABEL = 'Label';
    const NAME = 'Name';
    const IS_PRIMARY_KEY = 'IsPrimaryKey';
    const TYPE = 'Type';
    const FIELD_TYPE = 'FieldType';
    const BOOST = 'Boost';
    const FACET_HANDLER = 'FacetHandler';
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

    const FIELD_TYPE_FACET = 'facet';
    const FIELD_TYPE_KEYWORD = 'keyword';
    const FIELD_TYPE_UNINDEXED = 'unindexed';
    const FIELD_TYPE_TEXT = 'text';

    public function getFieldId(): int;

    public function setFieldId(int $value): self;

    public function getSyncGuid(): string;

    public function setSyncGuid(?string $value): self;

    public function getLabel(): string;

    public function setLabel(?string $value): self;

    public function getName(): string;

    public function setName(?string $value): self;

    public function getIsPrimaryKey(): bool;

    public function setIsPrimaryKey(bool $value): self;

    public function getType(): string;

    public function setType(?string $value): self;

    public function getFieldType(): string;

    public function setFieldType(?string $value): self;

    public function getBoost(): int;

    public function setBoost(int $value): self;

    public function getFacetHandler(): int;

    public function setFacetHandler(int $value): self;

    public function getIsOutput(): bool;

    public function setIsOutput(bool $value): self;

    public function getIsShingle(): bool;

    public function setIsShingle(bool $value): self;

    public function getIsBestFragment(): bool;

    public function setIsBestFragment(bool $value): self;

    public function getIsDictionary(): bool;

    public function setIsDictionary(bool $value): self;

    public function getIsSort(): bool;

    public function setIsSort(bool $value): self;

    public function getIsPrefix(): bool;

    public function setIsPrefix(bool $value): self;

    public function getIsHidden(): bool;

    public function setIsHidden(bool $value): self;

    public function getIsCompare(): bool;

    public function setIsCompare(bool $value): self;

    public function getSortOrder(): int;

    public function setSortOrder(int $value): self;

    public function getPartialQuery(): string;

    public function setPartialQuery(?string $value): self;

    public function getIsKeywordText(): bool;

    public function setIsKeywordText(bool $value): self;

    public function getIsQuery(): bool;

    public function setIsQuery(bool $value): self;

    public function getIsQueryText(): bool;

    public function setIsQueryText(bool $value): self;

    public function getSkipCustom(): bool;

    public function setSkipCustom(bool $value): self;

    public function getStripHtml(): bool;

    public function setStripHtml(bool $value): self;

    public function getMinNGramAnalyzer(): int;

    public function setMinNGramAnalyzer(int $value): self;

    public function getMaxNGramAnalyzer(): int;

    public function setMaxNGramAnalyzer(int $value): self;

    public function getCoordinateType(): int;

    public function setCoordinateType(int $value): self;

    public function getOmitNorms(): bool;

    public function setOmitNorms(bool $value): self;

    public function getItemMapping(): string;

    public function setItemMapping(?string $value): self;

    public function getDefaultValue(): string;

    public function setDefaultValue(?string $value): self;

    public function getUseForPrediction(): bool;

    public function setUseForPrediction(bool $value): self;

    public function getCopyTo(): string;

    public function setCopyTo(?string $value): self;

    public function getAnalyzer(): string;

    public function setAnalyzer(?string $value): self;

    public function getDoNotStore(): bool;

    public function setDoNotStore(bool $value): self;

    public function getTags(): string;

    public function setTags(?string $value): self;

    /**
     * @return int[]
     */
    public function getIterations(): array;

    /**
     * @param int[]|null $value
     */
    public function setIterations(?array $value): self;

    public function getAnalyzerLanguage(): string;

    public function setAnalyzerLanguage(?string $value): self;

    public function getPreviewMapping(): string;

    public function setPreviewMapping(?string $value): self;

    public function getOmitTfAndPos(): bool;

    public function setOmitTfAndPos(bool $value): self;

    public function getCreateDate(): string;

    public function setCreateDate(?string $value): self;

    public function getModifyDate(): string;

    public function setModifyDate(?string $value): self;

    public function getIsChild(): bool;

    public function setIsChild(bool $value): self;

    public function getIsHierarchical(): bool;

    public function setIsHierarchical(bool $value): self;
}
