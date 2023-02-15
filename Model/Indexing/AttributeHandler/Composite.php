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

namespace HawkSearch\EsIndexing\Model\Indexing\AttributeHandler;

use HawkSearch\EsIndexing\Model\Indexing\AttributeHandlerInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;

class Composite implements AttributeHandlerInterface
{
    /**#@+
     * Constants
     */
    public const HANDLER_DEFAULT_NAME = '__DEFAULT_HANDLER__';
    /**#@-*/

    /**
     * @var string[]
     */
    protected $handlers = [
        self::HANDLER_DEFAULT_NAME => DataObjectHandler::class
    ];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * AttributeHandlerComposite constructor.
     * @param AttributeHandlerInterface[] $handlers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $handlers = []
    ) {
        $this->objectManager = $objectManager;
        $this->mergeTypes($handlers);
    }

    /**
     * Add or override handlers
     *
     * @param array $handlers
     * @return void
     */
    protected function mergeTypes(array $handlers)
    {
        foreach ($handlers as $handler) {
            if (isset($handler['attribute']) && isset($handler['class'])) {
                $this->handlers[$handler['attribute']] = $handler['class'];
            }
        }
    }

    /**
     * @inheritDoc
     * @param DataObject $item
     */
    public function handle(DataObject $item, string $attributeCode)
    {
        $handler = $this->getObject( $this->handlers[$attributeCode] ?? $this->handlers[self::HANDLER_DEFAULT_NAME]);

        return $handler->handle($item, $attributeCode);
    }

    /**
     * @param string $attributeCode
     * @return AttributeHandlerInterface
     */
    protected function getHandler(string $attributeCode)
    {
        return $this->getObject( $this->handlers[$attributeCode] ?? $this->handlers[self::HANDLER_DEFAULT_NAME]);
    }

    /**
     * @param string $instanceName
     * @return AttributeHandlerInterface
     */
    private function getObject(string $instanceName)
    {
        $instance = $this->objectManager->create($instanceName);
        if (!$instance instanceof AttributeHandlerInterface) {
            throw new \InvalidArgumentException(
                get_class($instance) . ' isn\'t instance of ' . AttributeHandlerInterface::class
            );
        }

        return $instance;
    }
}
