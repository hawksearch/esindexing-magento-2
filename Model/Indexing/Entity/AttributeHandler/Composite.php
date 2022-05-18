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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\AttributeHandler;

use HawkSearch\EsIndexing\Model\Indexing\Entity\AttributeHandlerInterface;
use Magento\Framework\DataObject;

class Composite implements AttributeHandlerInterface
{
    /**
     * @var AttributeHandlerInterface[]
     */
    protected $handlers = [];

    /**
     * AttributeHandlerComposite constructor.
     * @param AttributeHandlerInterface[] $handlers
     */
    public function __construct(
        array $handlers = []
    ) {
        $this->handlers = $handlers;
    }

    /**
     * @inheritDoc
     * @param DataObject $item
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        $handler = $this->getHandler($attributeCode);

        return $handler ? $handler->handle($item, $attributeCode) : null;
    }

    /**
     * @param string $attributeCode
     * @return AttributeHandlerInterface|null
     */
    protected function getHandler(string $attributeCode)
    {
        /** @var AttributeHandlerInterface $currentHandler */
        $currentHandler = $this->handlers['__DEFAULT_HANDLER__'] ?? null;
        $currentHandler = isset($this->handlers[$attributeCode]) ? $this->handlers[$attributeCode] : $currentHandler;

        return $currentHandler;
    }
}
