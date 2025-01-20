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

namespace HawkSearch\EsIndexing\Plugin\Eav\Entity\Attribute\Source\Table;

use Magento\Eav\Model\Entity\Attribute\Source\Table;

/**
* Performance workaround for issue https://github.com/magento/magento2/issues/38934
* Please note this around plugin doesn't call $proceed callable,
* and it will prevent the execution of all the plugins next in the chain and the original method call
*
* @link https://github.com/magento/magento2/issues/38934
*/
class SpecificOptionsPlugin
{
    /**
     * @param Table $subject
     * @param callable $proceed
     * @param list<int>|string $ids
     * @param bool $withEmpty
     * @return array
     * @noinspection PhpMissingParamTypeInspection
     */
    public function aroundGetSpecificOptions(Table $subject, callable $proceed, $ids, bool $withEmpty = true): array
    {
        $allOptions = $subject->getAllOptions(true);
        $emptyOption = array_shift($allOptions);
        $specificOptions = [];

        if (is_string($ids) && strpos($ids, ',') !== false) {
            $ids = explode(',', $ids);
        }

        if (!is_array($ids)) {
            $ids = (array)$ids;
        }

        if (count($allOptions) > 0) {
            foreach ($allOptions as $option) {
                if (isset($option['value']) && in_array($option['value'], $ids)) {
                    $specificOptions[] = $option;
                }
            }
        }

        if ($withEmpty) {
            $specificOptions = $this->addEmptyOption($specificOptions, $emptyOption);
        }

        return $specificOptions;
    }

    /**
     * @param list<array{label: string, value: string}> $options
     * @param array{label: '', value: ''} $emptyOption
     * @return array
     * @noinspection PhpMissingParamTypeInspection
     */
    private function addEmptyOption(array $options, $emptyOption): array
    {
        array_unshift($options, $emptyOption);
        return $options;
    }
}
