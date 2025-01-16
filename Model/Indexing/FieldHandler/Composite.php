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

use HawkSearch\Connector\Compatibility\PublicPropertyDeprecationTrait;
use HawkSearch\EsIndexing\Model\Indexing\FieldHandlerInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;

/**
 * @api
 * @since 0.8.0
 *
 * @template T of FieldHandlerInterface
 * @template TItem of DataObject
 * @phpstan-type HandlersMap  array<string, class-string<T>>
 * @phpstan-type HandlerSignature array{attribute?: key-of<HandlersMap>, class?: value-of<HandlersMap>}
 * @implements T<TItem>
 */
class Composite implements FieldHandlerInterface
{
    use PublicPropertyDeprecationTrait;

    private array $deprecatedPublicProperties = [
        'handlers' => [
            'since' => '0.8.0',
            'description' => 'Visibility changed to private. Set via constructor injection.'
        ],
    ];

    /**#@+
     * Constants
     */
    public const HANDLER_DEFAULT_NAME = '__DEFAULT_HANDLER__';
    /**#@-*/

    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @var HandlersMap
     * @private 0.8.0 Visibility changed to private. Set via constructor injection.
     */
    private array $handlers = [
        self::HANDLER_DEFAULT_NAME => DataObjectHandler::class
    ];

    /**
     * Composite constructor.
     *
     * @param array<array-key, HandlerSignature> $handlers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $handlers = []
    )
    {
        $this->objectManager = $objectManager;
        $this->mergeTypes($handlers);
    }

    /**
     * @param TItem $item
     * @param key-of<HandlersMap> $fieldName
     */
    public function handle(DataObject $item, string $fieldName)
    {
        $handler = $this->getHandler($fieldName);

        return $handler->handle($item, $fieldName);
    }

    /**
     * Add or override handlers
     *
     * @param array<array-key, HandlerSignature> $handlers
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
     * @param key-of<HandlersMap> $fieldName
     * @return T
     */
    protected function getHandler(string $fieldName): FieldHandlerInterface
    {
        return $this->getObject($this->handlers[$fieldName] ?? $this->handlers[self::HANDLER_DEFAULT_NAME]);
    }

    /**
     * @param value-of<HandlersMap> $instanceName
     * @return T
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
