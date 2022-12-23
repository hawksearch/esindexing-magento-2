<?php
/**
 * Copyright (c) 2022 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Model;

use Magento\Framework\App\CacheInterface as Cache;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;

class LandingPageRepository implements \HawkSearch\EsIndexing\Api\LandingPageRepositoryInterface
{
    private const CACHE_KEY = 'hawksearch_landing_pages';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * LandingPageRepository constructor.
     * @param StoreManagerInterface $storeManager
     * @param Cache $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Cache $cache,
        SerializerInterface $serializer
    ) {
        $this->storeManager = $storeManager;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function getByUrl(string $url)
    {
        // TODO: Implement getByUrl() method.
    }

    /**
     * @inheritDoc
     */
    public function get(int $id)
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     * @TODO move pages storage resource to MySQL
     * @TODO do not call Hawk API in repository
     */
    public function getList()
    {
        if (($serialized = $this->cache->load($this->getCacheKey()))) {
            $landingPages = $this->serializer->unserialize($serialized);
        } else {
            $landingPages = $this->getHawkResponse(Zend_Http_Client::GET, 'LandingPage/Urls') ?: [];
            sort($landingPages, SORT_STRING);
            $this->cache->save(
                $this->serializer->serialize($landingPages),
                $this->getLPCacheKey(),
                [],
                $this->proxyConfigProvider->getLandingPagesCache()
            );
        }
        return $landingPages;
    }

    /**
     * @TODO move pages storage resource to MySQL
     * @throws NoSuchEntityException
     */
    private function getCacheKey()
    {
        return self::CACHE_KEY . $this->storeManager->getStore()->getId();
    }
}
