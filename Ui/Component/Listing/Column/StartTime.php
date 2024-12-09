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

namespace HawkSearch\EsIndexing\Ui\Component\Listing\Column;

use HawkSearch\EsIndexing\Model\ResourceModel\AsynchronousOperations\Operation;
use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Listing\Columns\Date;

class StartTime extends Date
{
    /**
     * @var Operation
     */
    private $operation;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TimezoneInterface $timezone
     * @param BooleanUtils $booleanUtils
     * @param Operation $operation
     * @param array<string, UiComponentInterface> $components
     * @param array<mixed> $data
     * @param ResolverInterface|null $localeResolver
     * @param DataBundle|null $dataBundle
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TimezoneInterface $timezone,
        BooleanUtils $booleanUtils,
        Operation $operation,
        array $components = [],
        array $data = [],
        ResolverInterface $localeResolver = null,
        DataBundle $dataBundle = null
    ) {
        $this->operation = $operation;
        parent::__construct(
            $context,
            $uiComponentFactory,
            $timezone,
            $booleanUtils,
            $components,
            $data,
            $localeResolver,
            $dataBundle
        );
    }

    /**
     * @inheritDoc
     */
    public function prepare()
    {
        parent::prepare();

        $config = (array)$this->getData('config');
        if (!$this->operation->isStartedAtColumnExists()) {
            $config['componentDisabled'] = true;
        }
        $this->setData('config', $config);
    }
}
