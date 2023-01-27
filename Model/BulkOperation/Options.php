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
namespace HawkSearch\EsIndexing\Model\BulkOperation;

use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => OperationInterface::STATUS_TYPE_COMPLETE,
                'label' =>  __('Complite')
            ],
            [
                'value' => OperationInterface::STATUS_TYPE_RETRIABLY_FAILED,
                'label' => __('Failed Retriably')
            ],
            [
                'value' => OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED,
                'label' => __('Failed Not Retriably')
            ],
            [
                'value' => OperationInterface::STATUS_TYPE_OPEN,
                'label' => __('Not Started')
            ],
            [
                'value' => OperationInterface::STATUS_TYPE_REJECTED,
                'label' => __('Rejected')
            ]
        ];
    }
}
