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

namespace HawkSearch\EsIndexing\Model\MessageQueue;

use HawkSearch\EsIndexing\Model\Indexing\EntityType\HierarchyEntityType;

class HierarchyEntityMessageTopicResolver implements MessageTopicResolverInterface
{
    const TOPIC = 'hawksearch.indexing.hierarchy.reindex';

    /**
     * @inheritDoc
     * @param HierarchyEntityType $object
     */
    public function resolve($object)
    {
        if ($object instanceof HierarchyEntityType) {
            return self::TOPIC;
        }

        return '';
    }
}
