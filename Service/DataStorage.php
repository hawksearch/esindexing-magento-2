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

/**
 * @api
 * @since 0.8.0
 */
class DataStorage implements DataStorageInterface
{
    private string $name;
    private mixed $value;

    /**
     * @param string|null $name
     * @throws RuntimeException
     * @todo $name: remove default value 'null'
     */
    public function __construct(string $name = null)
    {
        if ($name === null) {
            throw new RuntimeException(
                __(
                    'DataStorage name should be defined for class %s',
                    static::class
                )
            );
        }
        $this->name = $name;
    }

    /**
     * @return void
     * @throws RuntimeException
     */
    public function set(mixed $value, bool $graceful = false)
    {
        if (isset($this->value)) {
            if ($graceful) {
                return;
            }
            throw new RuntimeException(__(
                'Value in DataStorage \'%s\' has been already defined', $this->name));
        }
        $this->value = $value;
    }

    /**
     * @return void
     */
    public function reset()
    {
        if (isset($this->value)) {
            if (is_object($this->value)
                && method_exists($this->value, '__destruct')
                && is_callable([$this->value, '__destruct'])) {
                $this->value->__destruct();
            }
            unset($this->value);
        }
    }

    /**
     * @return mixed
     */
    public function get(bool $reset = false)
    {
        $value = null;
        if (isset($this->value)) {
            $value = $this->value;

            if ($reset) {
                $this->reset();
            }
        }

        return $value;
    }

    /**
     * Reset DataStorage value
     */
    public function __destruct()
    {
        $this->reset();
    }
}
