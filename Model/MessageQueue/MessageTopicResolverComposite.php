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

namespace HawkSearch\EsIndexing\Model\MessageQueue;

use Magento\Framework\Exception\InputException;

class MessageTopicResolverComposite implements MessageTopicResolverInterface
{
    /**
     * @var MessageTopicResolverInterface[]
     */
    private array $resolvers;

    /**
     * MessageTopicResolverComposite constructor.
     *
     * @param MessageTopicResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers = [])
    {
        $this->resolvers = $resolvers;
    }

    /**
     * @return string
     * @throws InputException
     */
    public function resolve(object $object)
    {
        foreach ($this->resolvers as $resolver) {
            $resolvedTopic = $resolver->resolve($object);
            if ($resolvedTopic) {
                return $resolvedTopic;
            }
        }
        throw new InputException(__('Can not resolve message topic'));
    }
}
