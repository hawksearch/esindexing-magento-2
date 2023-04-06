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

namespace HawkSearch\EsIndexing\Model\Indexer\Entities;

use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;
use Magento\Store\Api\Data\StoreInterface;

class SchedulerComposite implements SchedulerInterface
{
    /**
     * @var SchedulerInterface[]  | TMap
     */
    protected $schedulers = [];

    /**
     * SchedulerComposite constructor.
     *
     * @param SchedulerInterface[] $schedulers
     */
    public function __construct(
        TMapFactory $tmapFactory,
        array $schedulers = []
    ) {
        $this->schedulers = $tmapFactory->create(
            [
                'array' => $schedulers,
                'type' => SchedulerInterface::class
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function schedule(StoreInterface $store, ?array $ids = null)
    {
        foreach ($this->schedulers as $scheduler) {
            $scheduler->schedule($store, $ids);
        }
    }
}
