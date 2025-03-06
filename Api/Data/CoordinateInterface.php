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
 * Coordinate Interface used in ClientData
 *
 * @api v2
 * @since 0.8.0
 * @link https://developerdocs.hawksearch.com/reference/searchv2_search-1
 * @link https://searchapi-dev.hawksearch.net/swagger/ui/index#!/SearchV2/SearchV2_Search
 */
interface CoordinateInterface
{
    const FIELD_LATITUDE = 'Latitude';
    const FIELD_LONGITUDE = 'Longitude';

    /**
     * @return float
     */
    public function getLatitude(): float;

    /**
     * @return $this
     */
    public function setLatitude(float $value): self;

    /**
     * @return float
     */
    public function getLongitude(): float;

    /**
     * @return $this
     */
    public function setLongitude(float $value): self;
}
