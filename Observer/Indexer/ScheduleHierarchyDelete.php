<?php
/**
 * Copyright (c) 2021 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Observer\Indexer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ScheduleHierarchyDelete implements ObserverInterface
{
    /**
     * The observer is used for deleting the whole hierarchy structure before full items reindexing
     * Currently the deletion of all items is not needed because we have a new empty index
     * when each reindexing process starts. So that the observer is not used yet.
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        //It is not used yet
    }
}
