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

namespace HawkSearch\EsIndexing\Service;

use Magento\Framework\Exception\RuntimeException;

interface DataStorageInterface
{
    /**
     * Set DataStorage value
     *
     * @param mixed $value
     * @param bool $graceful
     * @throws RuntimeException
     */
    public function set($value, $graceful = false);

    /**
     * Reset DataStorage value
     */
    public function reset();

    /**
     * Retrieve a value from DataStorage
     *
     * @param bool $reset reset value after retrieving
     * @return mixed
     */
    public function get($reset = false);
}
