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

/**
 * @api
 * @since 0.8.0
 */
class MessageTopicByObjectResolver implements MessageTopicResolverInterface
{
    /**
     * @var string
     */
    private $topic;

    /**
     * @var string
     */
    private $resolverClass;

    public function __construct(
        string $topic,
        string $resolverClass
    ) {
        $this->topic = $topic;
        $this->resolverClass = $resolverClass;
    }

    /**
     * @inheritDoc
     */
    public function resolve(object $object)
    {
        return $object instanceof $this->resolverClass ? $this->topic : '';
    }
}
