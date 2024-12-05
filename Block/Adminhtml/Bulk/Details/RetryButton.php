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

namespace HawkSearch\EsIndexing\Block\Adminhtml\Bulk\Details;

use Magento\AsynchronousOperations\Model\Operation\Details;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * @api
 * @since 0.8.0
 */
class RetryButton implements ButtonProviderInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var Details
     */
    private Details $details;

    public function __construct(
        Details $details,
        RequestInterface $request
    ) {
        $this->details = $details;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        $uuid = $this->request->getParam('uuid');
        $details = $this->details->getDetails($uuid);
        if ($details['operations_failed'] === 0) {
            return [];
        }
        return [
            'label' => __('Retry'),
            'class' => 'retry primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
        ];
    }
}
