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
 * BoostQuery Interface used in SearchRequest
 */
interface BoostQueryInterface
{
    /**#@+
     * Constants for keys of data array
     */
    public const FIELD_QUERY = 'Query';
    public const FIELD_BOOST = 'Boost';
    /**#@-*/

    /**
     * @return string
     */
    public function getQuery(): string;

    /**
     * @param string|null $value
     * @return $this
     */
    public function setQuery(?string $value): self;

    /**
     * @return float
     */
    public function getBoost(): float;

    /**
     * @param float $value
     * @return $this
     */
    public function setBoost(float $value): self;
}
