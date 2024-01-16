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

namespace HawkSearch\EsIndexing\Gateway\Http;

use HawkSearch\Connector\Gateway\Http\Transfer;
use HawkSearch\Connector\Gateway\Http\TransferBuilder as TransferBuilderParent;
use HawkSearch\Connector\Gateway\Http\TransferInterface;
use Magento\Framework\App\ObjectManager;

/**
 * @api
 */
class TransferBuilder extends TransferBuilderParent
{
    /**
     * @return TransferInterface
     * @todo Move changes to parent
     * @see \HawkSearch\Connector\Gateway\Http\TransferBuilder::build
     */
    public function build()
    {
        $transfer = parent::build();
        return ObjectManager::getInstance()->create(Transfer::class, [
            'clientConfig' => $transfer->getClientConfig(),
            'headers' => $transfer->getHeaders(),
            'body'  => $transfer->getBody(),
            'auth' => [
                Transfer::AUTH_USERNAME => $transfer->getAuthUsername(),
                Transfer::AUTH_PASSWORD => $transfer->getAuthPassword(),
            ],
            'method' => $transfer->getMethod(),
            'uri' => $transfer->getUri(),
            'encode' => $transfer->shouldEncode()
        ]);

    }
}
