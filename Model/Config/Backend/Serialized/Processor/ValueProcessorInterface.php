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

namespace HawkSearch\EsIndexing\Model\Config\Backend\Serialized\Processor;

use Magento\Framework\App\Config\ValueInterface;

interface ValueProcessorInterface
{
    /**#@+
     * Constants
     */
    const COLUMN_ATTRIBUTE = 'attribute';
    const COLUMN_FIELD = 'field';
    const COLUMN_FIELD_NEW = 'field_new';
    const SELECT_OPTION_NEW_FILED_VALUE = '--insert--new--';
    /**#@-*/

    /**
     * Process config value before serialization
     *
     * @param array $value
     * @param ValueInterface $configValue
     * @return array
     */
    public function process(array $value, ValueInterface $configValue): array;
}
