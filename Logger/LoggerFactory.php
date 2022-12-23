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

namespace HawkSearch\EsIndexing\Logger;

use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Factory produces logger based on runtime configuration.
 *
 * phpcs:disable MEQP2.Classes.ObjectManager
 */
class LoggerFactory implements LoggerFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var LoggerConfigInterface
     */
    private $loggerConfig;

    /**
     * Instance name to create
     *
     * @var string
     */
    private $instanceName = null;

    /**
     * DebuggerFactory constructor.
     *
     * @param objectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        LoggerConfigInterface $loggerConfig,
        $instanceName = '\\Psr\\Log\\LoggerInterface'
    ) {
        $this->objectManager = $objectManager;
        $this->loggerConfig = $loggerConfig;
        $this->instanceName = $instanceName;
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        if (!$this->loggerConfig->isEnabled()) {
            return $this->objectManager->get(NullLogger::class);
        }

        $object = $this->objectManager->get($this->instanceName);

        if (!($object instanceof LoggerInterface)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s constructor expects the $instanceName to implement %s; received %s',
                    self::class,
                    LoggerInterface::class,
                    get_class($object)
                )
            );
        }


        return $object;
    }
}
