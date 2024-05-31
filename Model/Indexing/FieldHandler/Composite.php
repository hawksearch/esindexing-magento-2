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

namespace HawkSearch\EsIndexing\Model\Indexing\FieldHandler;

use HawkSearch\EsIndexing\Model\Indexing\FieldHandlerInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;

class Composite implements FieldHandlerInterface
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
     * Composite constructor.
     *
     * @param FieldHandlerInterface[] $handlers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $handlers = []
    ) {
        $this->objectManager = $objectManager;
        $this->mergeTypes($handlers);
    }

    /**
     * @inheritDoc
     * @param DataObject $item
     */
    public function handle(DataObject $item, string $fieldName)
    {
        $handler = $this->getHandler($fieldName);

        return $handler->handle($item, $fieldName);
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
     * @param string $attributeCode
     * @return FieldHandlerInterface
     */
    protected function getHandler(string $attributeCode): FieldHandlerInterface
    {
        return $this->getObject( $this->handlers[$attributeCode] ?? $this->handlers[self::HANDLER_DEFAULT_NAME]);
    }

    /**
     * @param string $instanceName
     * @return FieldHandlerInterface
     * @throws \InvalidArgumentException
     */
    private function getObject(string $instanceName): FieldHandlerInterface
    {
        $instance = $this->objectManager->create($instanceName);
        if (!$instance instanceof FieldHandlerInterface) {
            throw new \InvalidArgumentException(
                get_class($instance) . ' isn\'t instance of ' . FieldHandlerInterface::class
            );
        }

        return $instance;
    }
}
