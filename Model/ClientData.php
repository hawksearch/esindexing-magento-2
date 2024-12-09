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

use HawkSearch\EsIndexing\Api\Data\ClientDataInterface;
use HawkSearch\EsIndexing\Api\Data\CoordinateInterface;
use HawkSearch\EsIndexing\Api\Data\CoordinateInterfaceFactory;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * ClientData Interface used in SearchRequest
 */
class ClientData extends AbstractSimpleObject implements ClientDataInterface
{
    /**
     * @var CoordinateInterfaceFactory
     */
    private CoordinateInterfaceFactory $coordinateFactory;

    /**
     * @param CoordinateInterfaceFactory $coordinate
     * @param array<self::*, mixed> $data
     */
    public function __construct(
        CoordinateInterfaceFactory $coordinate,
        array $data = []
    ) {
        parent::__construct($data);
        $this->coordinateFactory = $coordinate;
    }

    /**
     * @inheritDoc
     */
    public function getVisitorId(): string
    {
        return (string)$this->_get(self::FIELD_VISITOR_ID);
    }

    /**
     * @inheritDoc
     */
    public function setVisitorId(?string $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_VISITOR_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getVisitId(): string
    {
        return (string)$this->_get(self::FIELD_VISIT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setVisitId(?string $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_VISIT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCustom(): array
    {
        return $this->_get(self::FIELD_CUSTOM) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setCustom(?array $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_CUSTOM, $value);
    }

    /**
     * @inheritDoc
     */
    public function getExtendedCustom(): array
    {
        return $this->_get(self::FIELD_EXTENDED_CUSTOM) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setExtendedCustom(?array $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_EXTENDED_CUSTOM, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPreviewBuckets(): array
    {
        return $this->_get(self::FIELD_PREVIEW_BUCKETS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setPreviewBuckets(?array $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_PREVIEW_BUCKETS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSource(): string
    {
        return (string)$this->_get(self::FIELD_SOURCE);
    }

    /**
     * @inheritDoc
     */
    public function setSource(?string $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_SOURCE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOrigin(): CoordinateInterface
    {
        return $this->_get(self::FIELD_ORIGIN) ?? $this->coordinateFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function setOrigin(?CoordinateInterface $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_ORIGIN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getZipCode(): string
    {
        return (string)$this->_get(self::FIELD_ZIP_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setZipCode(?string $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_ZIP_CODE, $value);
    }
}
