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
     *
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
    )
    {
        parent::__construct($data);
    }

    public function getFieldId(): int
    {
        return (int)$this->_get(static::FIELD_ID);
    }

    public function setFieldId(int $value): FieldInterface
    {
        return $this->setData(static::FIELD_ID, $value);
    }

    public function getSyncGuid(): string
    {
        return (string)$this->_get(static::SYNC_GUID);
    }

    public function setSyncGuid(?string $value): FieldInterface
    {
        return $this->setData(static::SYNC_GUID, $value);
    }

    public function getName(): string
    {
        return (string)$this->_get(static::NAME);
    }

    public function setName(?string $value): FieldInterface
    {
        return $this->setData(static::NAME, $value);
    }

    public function getFieldType(): string
    {
        return (string)$this->_get(static::FIELD_TYPE);
    }

    public function setFieldType(?string $value): FieldInterface
    {
        return $this->setData(static::FIELD_TYPE, $value);
    }

    public function getLabel(): string
    {
        return (string)$this->_get(static::LABEL);
    }

    public function setLabel(?string $value): FieldInterface
    {
        return $this->setData(static::LABEL, $value);
    }

    public function getType(): string
    {
        return (string)$this->_get(static::TYPE);
    }

    public function setType(?string $value): FieldInterface
    {
        return $this->setData(static::TYPE, $value);
    }

    public function getBoost(): int
    {
        return (int)$this->_get(static::BOOST);
    }

    public function setBoost(int $value): FieldInterface
    {
        return $this->setData(static::BOOST, $value);
    }

    public function getFacetHandler(): int
    {
        return (int)$this->_get(static::FACET_HANDLER);
    }

    public function setFacetHandler(int $value): FieldInterface
    {
        return $this->setData(static::FACET_HANDLER, $value);
    }

    public function getIsPrimaryKey(): bool
    {
        return !!$this->_get(static::IS_PRIMARY_KEY);
    }

    public function setIsPrimaryKey(bool $value): FieldInterface
    {
        return $this->setData(static::IS_PRIMARY_KEY, $value);
    }

    public function getIsOutput(): bool
    {
        return !!$this->_get(static::IS_OUTPUT);
    }

    public function setIsOutput(bool $value): FieldInterface
    {
        return $this->setData(static::IS_OUTPUT, $value);
    }

    public function getIsShingle(): bool
    {
        return !!$this->_get(static::IS_SHINGLE);
    }

    public function setIsShingle(bool $value): FieldInterface
    {
        return $this->setData(static::IS_SHINGLE, $value);
    }

    public function getIsBestFragment(): bool
    {
        return !!$this->_get(static::IS_BEST_FRAGMENT);
    }

    public function setIsBestFragment(bool $value): FieldInterface
    {
        return $this->setData(static::IS_BEST_FRAGMENT, $value);
    }

    public function getIsDictionary(): bool
    {
        return !!$this->_get(static::IS_DICTIONARY);
    }

    public function setIsDictionary(bool $value): FieldInterface
    {
        return $this->setData(static::IS_DICTIONARY, $value);
    }

    public function getIsSort(): bool
    {
        return !!$this->_get(static::IS_SORT);
    }

    public function setIsSort(bool $value): FieldInterface
    {
        return $this->setData(static::IS_SORT, $value);
    }

    public function getIsPrefix(): bool
    {
        return !!$this->_get(static::IS_PREFIX);
    }

    public function setIsPrefix(bool $value): FieldInterface
    {
        return $this->setData(static::IS_PREFIX, $value);
    }

    public function getIsHidden(): bool
    {
        return !!$this->_get(static::IS_HIDDEN);
    }

    public function setIsHidden(bool $value): FieldInterface
    {
        return $this->setData(static::IS_HIDDEN, $value);
    }

    public function getIsCompare(): bool
    {
        return !!$this->_get(static::IS_COMPARE);
    }

    public function setIsCompare(bool $value): FieldInterface
    {
        return $this->setData(static::IS_COMPARE, $value);
    }

    public function getSortOrder(): int
    {
        return (int)$this->_get(static::SORT_ORDER);
    }

    public function setSortOrder(int $value): FieldInterface
    {
        return $this->setData(static::SORT_ORDER, $value);
    }

    public function getPartialQuery(): string
    {
        return (string)$this->_get(static::PARTIAL_QUERY);
    }

    public function setPartialQuery(?string $value): FieldInterface
    {
        return $this->setData(static::PARTIAL_QUERY, $value);
    }

    public function getIsKeywordText(): bool
    {
        return !!$this->_get(static::IS_KEYWORD_TEXT);
    }

    public function setIsKeywordText(bool $value): FieldInterface
    {
        return $this->setData(static::IS_KEYWORD_TEXT, $value);
    }

    public function getIsQuery(): bool
    {
        return !!$this->_get(static::IS_QUERY);
    }

    public function setIsQuery(bool $value): FieldInterface
    {
        return $this->setData(static::IS_QUERY, $value);
    }

    public function getIsQueryText(): bool
    {
        return !!$this->_get(static::IS_QUERY_TEXT);
    }

    public function setIsQueryText(bool $value): FieldInterface
    {
        return $this->setData(static::IS_QUERY_TEXT, $value);
    }

    public function getSkipCustom(): bool
    {
        return !!$this->_get(static::SKIP_CUSTOM);
    }

    public function setSkipCustom(bool $value): FieldInterface
    {
        return $this->setData(static::SKIP_CUSTOM, $value);
    }

    public function getStripHtml(): bool
    {
        return !!$this->_get(static::STRIP_HTML);
    }

    public function setStripHtml(bool $value): FieldInterface
    {
        return $this->setData(static::STRIP_HTML, $value);
    }

    public function getMinNGramAnalyzer(): int
    {
        return (int)$this->_get(static::MIN_N_GRAM_ANALYZER);
    }

    public function setMinNGramAnalyzer(int $value): FieldInterface
    {
        return $this->setData(static::MIN_N_GRAM_ANALYZER, $value);
    }

    public function getMaxNGramAnalyzer(): int
    {
        return (int)$this->_get(static::MAX_N_GRAM_ANALYZER);
    }

    public function setMaxNGramAnalyzer(int $value): FieldInterface
    {
        return $this->setData(static::MAX_N_GRAM_ANALYZER, $value);
    }

    public function getCoordinateType(): int
    {
        return (int)$this->_get(static::COORDINATE_TYPE);
    }

    public function setCoordinateType(int $value): FieldInterface
    {
        return $this->setData(static::COORDINATE_TYPE, $value);
    }

    public function getOmitNorms(): bool
    {
        return !!$this->_get(static::OMIT_NORMS);
    }

    public function setOmitNorms(bool $value): FieldInterface
    {
        return $this->setData(static::OMIT_NORMS, $value);
    }

    public function getItemMapping(): string
    {
        return (string)$this->_get(static::ITEM_MAPPING);
    }

    public function setItemMapping(?string $value): FieldInterface
    {
        return $this->setData(static::ITEM_MAPPING, $value);
    }

    public function getDefaultValue(): string
    {
        return (string)$this->_get(static::DEFAULT_VALUE);
    }

    public function setDefaultValue(?string $value): FieldInterface
    {
        return $this->setData(static::DEFAULT_VALUE, $value);
    }

    public function getUseForPrediction(): bool
    {
        return !!$this->_get(static::USE_FOR_PREDICTION);
    }

    public function setUseForPrediction(bool $value): FieldInterface
    {
        return $this->setData(static::USE_FOR_PREDICTION, $value);
    }

    public function getCopyTo(): string
    {
        return (string)$this->_get(static::COPY_TO);
    }

    public function setCopyTo(?string $value): FieldInterface
    {
        return $this->setData(static::COPY_TO, $value);
    }

    public function getAnalyzer(): string
    {
        return (string)$this->_get(static::ANALYZER);
    }

    public function setAnalyzer(?string $value): FieldInterface
    {
        return $this->setData(static::ANALYZER, $value);
    }

    public function getDoNotStore(): bool
    {
        return !!$this->_get(static::DO_NOT_STORE);
    }

    public function setDoNotStore(bool $value): FieldInterface
    {
        return $this->setData(static::DO_NOT_STORE, $value);
    }

    public function getTags(): string
    {
        return (string)$this->_get(static::TAGS);
    }

    public function setTags(?string $value): FieldInterface
    {
        return $this->setData(static::TAGS, $value);
    }

    public function getIterations(): array
    {
        return $this->_get(static::ITERATIONS) ?? [];
    }

    public function setIterations(?array $value): FieldInterface
    {
        return $this->setData(static::ITERATIONS, $value);
    }

    public function getAnalyzerLanguage(): string
    {
        return (string)$this->_get(static::ANALYZER_LANGUAGE);
    }

    public function setAnalyzerLanguage(?string $value): FieldInterface
    {
        return $this->setData(static::ANALYZER_LANGUAGE, $value);
    }

    public function getPreviewMapping(): string
    {
        return (string)$this->_get(static::PREVIEW_MAPPING);
    }

    public function setPreviewMapping(?string $value): FieldInterface
    {
        return $this->setData(static::PREVIEW_MAPPING, $value);
    }

    public function getOmitTfAndPos(): bool
    {
        return !!$this->_get(static::OMIT_TF_ADN_POS);
    }

    public function setOmitTfAndPos(bool $value): FieldInterface
    {
        return $this->setData(static::OMIT_TF_ADN_POS, $value);
    }

    public function getCreateDate(): string
    {
        return (string)$this->_get(static::CREATE_DATE);
    }

    public function setCreateDate(?string $value): FieldInterface
    {
        return $this->setData(static::CREATE_DATE, $value);
    }

    public function getModifyDate(): string
    {
        return (string)$this->_get(static::MODIFY_DATE);
    }

    public function setModifyDate(?string $value): FieldInterface
    {
        return $this->setData(static::MODIFY_DATE, $value);
    }

    public function getIsChild(): bool
    {
        return !!$this->_get(static::IS_CHILD);
    }

    public function setIsChild(bool $value): FieldInterface
    {
        return $this->setData(static::IS_CHILD, $value);
    }

    public function getIsHierarchical(): bool
    {
        return !!$this->_get(static::IS_HIERARCHICAL);
    }

    public function setIsHierarchical(bool $value): FieldInterface
    {
        return $this->setData(static::IS_HIERARCHICAL, $value);
    }
}
