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

namespace HawkSearch\EsIndexing\Model\LandingPage\Field\Handler;

use HawkSearch\Connector\Helper\DataObjectHelper;
use HawkSearch\EsIndexing\Model\Indexing\FieldHandler\DataObjectHandler;
use Magento\Framework\DataObject;

class DefaultHandler extends DataObjectHandler
{
    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    public function __construct(DataObjectHelper $dataObjectHelper)
    {
        $this->dataObjectHelper = $dataObjectHelper;
    }

    public function handle(DataObject $item, string $fieldName)
    {
        $fieldName = $this->dataObjectHelper->camelCaseToSnakeCase($fieldName);
        return parent::handle($item, $fieldName);
    }
}
