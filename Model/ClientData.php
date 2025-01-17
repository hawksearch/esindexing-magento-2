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

class ClientData extends AbstractSimpleObject implements ClientDataInterface
{
    private CoordinateInterfaceFactory $coordinateFactory;

    /**
     * @param CoordinateInterfaceFactory $coordinate
     * @param array<self::*, mixed> $data
     */
    public function __construct(
        CoordinateInterfaceFactory $coordinate,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->coordinateFactory = $coordinate;
    }

    public function getVisitorId(): string
    {
        return (string)$this->_get(self::FIELD_VISITOR_ID);
    }

    public function setVisitorId(?string $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_VISITOR_ID, $value);
    }

    public function getVisitId(): string
    {
        return (string)$this->_get(self::FIELD_VISIT_ID);
    }

    public function setVisitId(?string $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_VISIT_ID, $value);
    }

    public function getCustom(): array
    {
        return (array)($this->_get(self::FIELD_CUSTOM) ?? []);
    }

    public function setCustom(?array $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_CUSTOM, $value);
    }

    public function getExtendedCustom(): array
    {
        return (array)($this->_get(self::FIELD_EXTENDED_CUSTOM) ?? []);
    }

    public function setExtendedCustom(?array $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_EXTENDED_CUSTOM, $value);
    }

    public function getPreviewBuckets(): array
    {
        return (array)($this->_get(self::FIELD_PREVIEW_BUCKETS) ?? []);
    }

    public function setPreviewBuckets(?array $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_PREVIEW_BUCKETS, $value);
    }

    public function getSource(): string
    {
        return (string)$this->_get(self::FIELD_SOURCE);
    }

    public function setSource(?string $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_SOURCE, $value);
    }

    public function getOrigin(): CoordinateInterface
    {
        return $this->_get(self::FIELD_ORIGIN) ?? $this->coordinateFactory->create();
    }

    public function setOrigin(?CoordinateInterface $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_ORIGIN, $value);
    }

    public function getZipCode(): string
    {
        return (string)$this->_get(self::FIELD_ZIP_CODE);
    }

    public function setZipCode(?string $value): ClientDataInterface
    {
        return $this->setData(self::FIELD_ZIP_CODE, $value);
    }
}
