<?php
/**
 *  Copyright (c) 2020 Hawksearch (www.hawksearch.com) - All Rights Reserved
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 *  FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 *  IN THE SOFTWARE.
 */
declare(strict_types=1);

namespace HawkSearch\EsIndexing\Model\Indexing;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;

class ItemsProviderPool implements ItemsProviderPoolInterface
{
    /**
     * @var ItemsProviderInterface[] | TMap
     */
    private $providers;

    /**
     * @param TMapFactory $tmapFactory
     * @param array $providers
     */
    public function __construct(
        TMapFactory $tmapFactory,
        array $providers = []
    ) {
        $this->providers = $tmapFactory->createSharedObjectsMap(
            [
                'array' => $providers,
                'type' => ItemsProviderInterface::class
            ]
        );
    }

    /**
     * Returns items executor for defined provider
     *
     * @param string $providerCode
     * @return ItemsProviderInterface
     * @throws NotFoundException
     */
    public function get($providerCode)
    {
        if (!isset($this->providers[$providerCode])) {
            throw new NotFoundException(
                __(
                    'The "%1" provider isn\'t defined. Verify the provider code and try again.',
                    $providerCode
                )
            );
        }

        return $this->providers[$providerCode];
    }
}
