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

namespace HawkSearch\EsIndexing\Setup\Patch\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class FieldMappingConfigPatch implements DataPatchInterface
{
    const CONFIG_PATH_ATTRIBUTES = 'hawksearch_product_settings/products/custom_attributes';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Json $json
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Json $json
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->json = $json;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function apply()
    {
        $configTable = $this->moduleDataSetup->getTable('core_config_data');
        $select = $this->moduleDataSetup->getConnection()->select()
            ->from($configTable)
            ->where('path = ?', self::CONFIG_PATH_ATTRIBUTES);
        $config = $this->moduleDataSetup->getConnection()->fetchRow($select);

        if (!empty($config)) {
            $unserialized = $this->json->unserialize($config['value']);

            $result = [];
            foreach ($unserialized as $fieldData) {
                $fieldData['field'] = $fieldData['field'] ?? $fieldData['attribute'];
                $result[] = $fieldData;
            }

            $this->moduleDataSetup->getConnection()->update(
                $configTable,
                ['value' => $this->json->serialize($result)],
                ['path = ?' => self::CONFIG_PATH_ATTRIBUTES]
            );
        }
    }
}
