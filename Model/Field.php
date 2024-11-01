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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\EsIndexing\Api\Data\FieldInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class Field extends AbstractSimpleObject implements FieldInterface
{
    /**
     * Field constructor.
     * @param array<self::*, mixed> $data
     */
    public function __construct(
        array $data = [
            self::NAME => '', // should be specified during field creation
            self::LABEL => '', // should be specified during field creation
            self::FIELD_TYPE => "keyword",
            self::TYPE => "String",
            self::BOOST => 1,
            self::FACET_HANDLER => 0,
            self::IS_PRIMARY_KEY => false,
            self::IS_OUTPUT => false,
            self::IS_SHINGLE => false,
            self::IS_BEST_FRAGMENT => false,
            self::IS_DICTIONARY => false,
            self::IS_SORT => false,
            self::IS_PREFIX => false,
            self::IS_HIDDEN => false,
            self::IS_COMPARE => false,
            self::IS_KEYWORD_TEXT => true,
            self::IS_QUERY => false,
            self::IS_QUERY_TEXT => false,
            self::SKIP_CUSTOM => false,
            self::STRIP_HTML => false
        ]
    ) {
        parent::__construct($data);
    }

    /**
     * @inheritDoc
     */
    public function getFieldId(): int
    {
        return (int)$this->_get(static::FIELD_ID);
    }

    /**
     * @inheritDoc
     */
    public function setFieldId(int $value): FieldInterface
    {
        return $this->setData(static::FIELD_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSyncGuid(): string
    {
        return (string)$this->_get(static::SYNC_GUID);
    }

    /**
     * @inheritDoc
     */
    public function setSyncGuid(?string $value): FieldInterface
    {
        return $this->setData(static::SYNC_GUID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->_get(static::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(?string $value): FieldInterface
    {
        return $this->setData(static::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFieldType(): string
    {
        return (string)$this->_get(static::FIELD_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setFieldType(?string $value): FieldInterface
    {
        return $this->setData(static::FIELD_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return (string)$this->_get(static::LABEL);
    }

    /**
     * @inheritDoc
     */
    public function setLabel(?string $value): FieldInterface
    {
        return $this->setData(static::LABEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string)$this->_get(static::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(?string $value): FieldInterface
    {
        return $this->setData(static::TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getBoost(): int
    {
        return (int)$this->_get(static::BOOST);
    }

    /**
     * @inheritDoc
     */
    public function setBoost(int $value): FieldInterface
    {
        return $this->setData(static::BOOST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFacetHandler(): int
    {
        return (int)$this->_get(static::FACET_HANDLER);
    }

    /**
     * @inheritDoc
     */
    public function setFacetHandler(int $value): FieldInterface
    {
        return $this->setData(static::FACET_HANDLER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsPrimaryKey(): bool
    {
        return !!$this->_get(static::IS_PRIMARY_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setIsPrimaryKey(bool $value): FieldInterface
    {
        return $this->setData(static::IS_PRIMARY_KEY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsOutput(): bool
    {
        return !!$this->_get(static::IS_OUTPUT);
    }

    /**
     * @inheritDoc
     */
    public function setIsOutput(bool $value): FieldInterface
    {
        return $this->setData(static::IS_OUTPUT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsShingle(): bool
    {
        return !!$this->_get(static::IS_SHINGLE);
    }

    /**
     * @inheritDoc
     */
    public function setIsShingle(bool $value): FieldInterface
    {
        return $this->setData(static::IS_SHINGLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsBestFragment(): bool
    {
        return !!$this->_get(static::IS_BEST_FRAGMENT);
    }

    /**
     * @inheritDoc
     */
    public function setIsBestFragment(bool $value): FieldInterface
    {
        return $this->setData(static::IS_BEST_FRAGMENT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsDictionary(): bool
    {
        return !!$this->_get(static::IS_DICTIONARY);
    }

    /**
     * @inheritDoc
     */
    public function setIsDictionary(bool $value): FieldInterface
    {
        return $this->setData(static::IS_DICTIONARY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsSort(): bool
    {
        return !!$this->_get(static::IS_SORT);
    }

    /**
     * @inheritDoc
     */
    public function setIsSort(bool $value): FieldInterface
    {
        return $this->setData(static::IS_SORT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsPrefix(): bool
    {
        return !!$this->_get(static::IS_PREFIX);
    }

    /**
     * @inheritDoc
     */
    public function setIsPrefix(bool $value): FieldInterface
    {
        return $this->setData(static::IS_PREFIX, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsHidden(): bool
    {
        return !!$this->_get(static::IS_HIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function setIsHidden(bool $value): FieldInterface
    {
        return $this->setData(static::IS_HIDDEN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsCompare(): bool
    {
        return !!$this->_get(static::IS_COMPARE);
    }

    /**
     * @inheritDoc
     */
    public function setIsCompare(bool $value): FieldInterface
    {
        return $this->setData(static::IS_COMPARE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder(): int
    {
        return (int)$this->_get(static::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder(int $value): FieldInterface
    {
        return $this->setData(static::SORT_ORDER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPartialQuery(): string
    {
        return (string)$this->_get(static::PARTIAL_QUERY);
    }

    /**
     * @inheritDoc
     */
    public function setPartialQuery(?string $value): FieldInterface
    {
        return $this->setData(static::PARTIAL_QUERY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsKeywordText(): bool
    {
        return !!$this->_get(static::IS_KEYWORD_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setIsKeywordText(bool $value): FieldInterface
    {
        return $this->setData(static::IS_KEYWORD_TEXT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsQuery(): bool
    {
        return !!$this->_get(static::IS_QUERY);
    }

    /**
     * @inheritDoc
     */
    public function setIsQuery(bool $value): FieldInterface
    {
        return $this->setData(static::IS_QUERY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsQueryText(): bool
    {
        return !!$this->_get(static::IS_QUERY_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setIsQueryText(bool $value): FieldInterface
    {
        return $this->setData(static::IS_QUERY_TEXT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSkipCustom(): bool
    {
        return !!$this->_get(static::SKIP_CUSTOM);
    }

    /**
     * @inheritDoc
     */
    public function setSkipCustom(bool $value): FieldInterface
    {
        return $this->setData(static::SKIP_CUSTOM, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStripHtml(): bool
    {
        return !!$this->_get(static::STRIP_HTML);
    }

    /**
     * @inheritDoc
     */
    public function setStripHtml(bool $value): FieldInterface
    {
        return $this->setData(static::STRIP_HTML, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMinNGramAnalyzer(): int
    {
        return (int)$this->_get(static::MIN_N_GRAM_ANALYZER);
    }

    /**
     * @inheritDoc
     */
    public function setMinNGramAnalyzer(int $value): FieldInterface
    {
        return $this->setData(static::MIN_N_GRAM_ANALYZER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMaxNGramAnalyzer(): int
    {
        return (int)$this->_get(static::MAX_N_GRAM_ANALYZER);
    }

    /**
     * @inheritDoc
     */
    public function setMaxNGramAnalyzer(int $value): FieldInterface
    {
        return $this->setData(static::MAX_N_GRAM_ANALYZER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCoordinateType(): int
    {
        return (int)$this->_get(static::COORDINATE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setCoordinateType(int $value): FieldInterface
    {
        return $this->setData(static::COORDINATE_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOmitNorms(): bool
    {
        return !!$this->_get(static::OMIT_NORMS);
    }

    /**
     * @inheritDoc
     */
    public function setOmitNorms(bool $value): FieldInterface
    {
        return $this->setData(static::OMIT_NORMS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getItemMapping(): string
    {
        return (string)$this->_get(static::ITEM_MAPPING);
    }

    /**
     * @inheritDoc
     */
    public function setItemMapping(?string $value): FieldInterface
    {
        return $this->setData(static::ITEM_MAPPING, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue(): string
    {
        return (string)$this->_get(static::DEFAULT_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultValue(?string $value): FieldInterface
    {
        return $this->setData(static::DEFAULT_VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUseForPrediction(): bool
    {
        return !!$this->_get(static::USE_FOR_PREDICTION);
    }

    /**
     * @inheritDoc
     */
    public function setUseForPrediction(bool $value): FieldInterface
    {
        return $this->setData(static::USE_FOR_PREDICTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCopyTo(): string
    {
        return (string)$this->_get(static::COPY_TO);
    }

    /**
     * @inheritDoc
     */
    public function setCopyTo(?string $value): FieldInterface
    {
        return $this->setData(static::COPY_TO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAnalyzer(): string
    {
        return (string)$this->_get(static::ANALYZER);
    }

    /**
     * @inheritDoc
     */
    public function setAnalyzer(?string $value): FieldInterface
    {
        return $this->setData(static::ANALYZER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDoNotStore(): bool
    {
        return !!$this->_get(static::DO_NOT_STORE);
    }

    /**
     * @inheritDoc
     */
    public function setDoNotStore(bool $value): FieldInterface
    {
        return $this->setData(static::DO_NOT_STORE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTags(): string
    {
        return (string)$this->_get(static::TAGS);
    }

    /**
     * @inheritDoc
     */
    public function setTags(?string $value): FieldInterface
    {
        return $this->setData(static::TAGS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIterations(): array
    {
        return $this->_get(static::ITERATIONS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setIterations(?array $value): FieldInterface
    {
        return $this->setData(static::ITERATIONS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAnalyzerLanguage(): string
    {
        return (string)$this->_get(static::ANALYZER_LANGUAGE);
    }

    /**
     * @inheritDoc
     */
    public function setAnalyzerLanguage(?string $value): FieldInterface
    {
        return $this->setData(static::ANALYZER_LANGUAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPreviewMapping(): string
    {
        return (string)$this->_get(static::PREVIEW_MAPPING);
    }

    /**
     * @inheritDoc
     */
    public function setPreviewMapping(?string $value): FieldInterface
    {
        return $this->setData(static::PREVIEW_MAPPING, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOmitTfAndPos(): bool
    {
        return !!$this->_get(static::OMIT_TF_ADN_POS);
    }

    /**
     * @inheritDoc
     */
    public function setOmitTfAndPos(bool $value): FieldInterface
    {
        return $this->setData(static::OMIT_TF_ADN_POS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCreateDate(): string
    {
        return (string)$this->_get(static::CREATE_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setCreateDate(?string $value): FieldInterface
    {
        return $this->setData(static::CREATE_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getModifyDate(): string
    {
        return (string)$this->_get(static::MODIFY_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setModifyDate(?string $value): FieldInterface
    {
        return $this->setData(static::MODIFY_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsChild(): bool
    {
        return !!$this->_get(static::IS_CHILD);
    }

    /**
     * @inheritDoc
     */
    public function setIsChild(bool $value): FieldInterface
    {
        return $this->setData(static::IS_CHILD, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsHierarchical(): bool
    {
        return !!$this->_get(static::IS_HIERARCHICAL);
    }

    /**
     * @inheritDoc
     */
    public function setIsHierarchical(bool $value): FieldInterface
    {
        return $this->setData(static::IS_HIERARCHICAL, $value);
    }
}
