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

namespace HawkSearch\EsIndexing\Console\Command;

use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;
use HawkSearch\EsIndexing\Api\Data\LandingPageInterfaceFactory;
use HawkSearch\EsIndexing\Api\LandingPageManagementInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SyncCategories
 * @package HawkSearch\EsIndexing\Console\Command
 * @TODO Refactor the class
 */
class SyncCategories extends Command
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @var LandingPageManagementInterface
     */
    private $landingPageManagement;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var LandingPageInterfaceFactory
     */
    private $landingPageInterfaceFactory;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var CategoryResource
     */
    private $categoryResource;

    /**
     * @param State $state
     * @param StoreManagerInterface $storeManager
     * @param Emulation $emulation
     * @param LandingPageManagementInterface $landingPageManagement
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param LandingPageInterfaceFactory $landingPageInterfaceFactory
     * @param CategoryFactory $categoryFactory
     * @param CategoryResource $categoryResource
     * @param string|null $name
     */
    public function __construct(
        State $state,
        StoreManagerInterface $storeManager,
        Emulation $emulation,
        LandingPageManagementInterface $landingPageManagement,
        CategoryCollectionFactory $categoryCollectionFactory,
        LandingPageInterfaceFactory $landingPageInterfaceFactory,
        CategoryFactory $categoryFactory,
        CategoryResource $categoryResource,
        string $name = null
    ) {
        parent::__construct($name);

        $this->state = $state;
        $this->storeManager = $storeManager;
        $this->emulation = $emulation;
        $this->landingPageManagement = $landingPageManagement;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->landingPageInterfaceFactory = $landingPageInterfaceFactory;
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
    }

    /**
     * @inheritDoc
     */
    protected function configure() : void
    {
        $this->setName('hawksearch:sync-categories')
            ->setDescription('Run the HawkSearch Category Sync Task');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $this->state->setAreaCode(Area::AREA_CRONTAB);

        try {
            $results = $this->syncCategories();
        } catch (\Exception $exception) {
            $output->writeln(__('An error occurred: %1', $exception->getMessage()));
        }

        if ($results) {
            foreach ($results as $res) {
                $output->writeln('An error occurred: ' . $res);
            }
        }
    }

    /**
     * @return array Errors if occure
     */
    protected function syncCategories()
    {
        $stores = $this->storeManager->getStores();
        $errors = [];
        foreach ($stores as $store) {
            /**
             * @var Store $store
             */
            try {
                $this->syncHawkLandingByStore($store);
            } catch (\Exception $e) {
                $errors[] = __("Error syncing category pages for store '%1'", $store->getCode());
                $errors[] = __("Exception message: %1", $e->getMessage());
                continue;
            }
        }
        return $errors;
    }

    /**
     * @param Store $store
     * @throws LocalizedException
     */
    private function syncHawkLandingByStore(Store $store)
    {
        $this->emulation->startEnvironmentEmulation($store->getId());
        /*
         * ok, so here is the problem, if we put or post,
         * and some landing page already has that "custom" value, we get
         * a duplicate error: {"Message":"Duplicate Custom field"}.
         * so lets create a new array "existingCustom" so we can
         * clear the custom value from the existing landing page.
         * we will need to trim that function at the end of each
         * iteration so we don't end up removing custom fields we just set */

        $hawkList = $this->landingPageManagement->getLandingPages();
        $existingCustom = $this->createExistingCustomFieldMap($hawkList);

        $mageList = $this->getMagentoLandingPages($store);

        usort(
            $hawkList,
            function ($a, $b) {
                /** @var LandingPageInterface $a */
                /** @var LandingPageInterface $b */
                return strcmp($a->getCustomUrl(), $b->getCustomUrl());
            }
        );
        usort(
            $mageList,
            function ($a, $b) {
                return strcmp($a['hawkurl'], $b['hawkurl']);
            }
        );

        $left = 0; //hawk on the left
        $right = 0; //magento on the right
        $deletePages = [];
        $addPages = [];
        $updatePages = [];
        while ($left < count($hawkList) || $right < count($mageList)) {
            if ($left >= count($hawkList)) {
                //only right left to process
                $sc = 1;
            } elseif ($right >= count($mageList)) {
                // only left left to process
                $sc = -1;
            } else {
                $sc = strcmp($hawkList[$left]->getCustomUrl(), $mageList[$right]['hawkurl']);
            }
            $customVal = null;
            if ($sc < 0) {
                //Hawk has page Magento doesn't want managed, delete, increment left
                if (substr($hawkList[$left]->getCustom(), 0, strlen('__mage_catid_')) == '__mage_catid_') {
                    /*$resp = $this->getHawkResponse(
                        Zend_Http_Client::DELETE,
                        self::HAWK_LANDING_PAGE_URL . $hawkList[$left]['pageid']
                    );
                    $this->validateHawkLandingPageResponse(
                        $resp,
                        Zend_Http_Client::DELETE,
                        $hawkList[$left]['hawkurl']
                    );*/
                    $deletePages[] = $hawkList[$left]->getPageId();

                } else {
                    /*$this->log(
                        sprintf(
                            'Customer custom landing page "%s", skipping',
                            $hawkList[$left]['hawkurl']
                        )
                    );*/
                }
                $customVal = $hawkList[$left]->getCustom();
                $left++;
            } elseif ($sc > 0) {
                //Mage wants it managed, but hawk doesn't know, POST and increment right
                $custom = "__mage_catid_" . $mageList[$right]['catid'] ."__";
                $addPages[] = $this->landingPageInterfaceFactory->create()
                    ->setName($mageList[$right]['name'])
                    ->setCustomUrl($mageList[$right]['hawkurl'])
                    ->setCustom($custom)
                    ->setIsFacetOverride(false)
                    ->setPageType('ProductListing')
                    ->setNarrowXml($this->getHawkNarrowXml($mageList[$right]['catid']));
                //$res = $this->landingPageManagement->updateLandingPages($landingPages);
                $right++;
            } else {
                //they are the same, PUT value to cover name changes, etc. increment both sides
                $custom = "__mage_catid_" . $mageList[$right]['catid'] ."__";
                $updatePages[] = $this->landingPageInterfaceFactory->create()
                    ->setName($mageList[$right]['name'])
                    ->setCustomUrl($mageList[$right]['hawkurl'])
                    ->setCustom($custom)
                    ->setIsFacetOverride(false)
                    ->setPageType('ProductListing')
                    ->setNarrowXml($this->getHawkNarrowXml($mageList[$right]['catid']))
                    ->setPageId($hawkList[$left]->getPageId());

                $left++;
                $right++;
            }
            if (isset($existingCustom[$customVal])) {
                unset($existingCustom[$customVal]);
            }
        }
        if ($deletePages) {
            $this->landingPageManagement->deleteLandingPages($deletePages);
        }

        if ($updatePages) {
            $this->landingPageManagement->updateLandingPages($updatePages);
        }

        if ($addPages) {
            $this->landingPageManagement->addLandingPages($addPages);
        }

        $this->emulation->stopEnvironmentEmulation();
    }

    /**
     * @param LandingPageInterface[] $hawklist
     * @return array
     */
    private function createExistingCustomFieldMap($hawklist)
    {
        $a = [];
        foreach ($hawklist as $item) {
            if ($item->getCustom() !== null) {
                $a[$item->getCustom()] = $item;
            }
        }
        return $a;
    }

    /**
     * @param Store $store
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMagentoLandingPages(Store $store)
    {
        $storeParentCategoryId = $this->storeManager->getStore($store->getId())->getRootCategoryId();

        /**
         * Check if parent node of the store still exists
         */
        /* @var $category CategoryModel */
        $category = $this->categoryFactory->create();
        $this->categoryResource->load($category, $storeParentCategoryId);

        if (!$category->getId()) {
            return [];
        }

        $pathRegexGroups = [
            //$category->getPath() . "$",
            $category->getPath() . "/"
        ];
        $pathFilterRegex = "(" . implode('|', $pathRegexGroups) . ")";
        $categories = $category->getCategories($category->getParentId(), 0, false, true, false);
        $categories->addPathFilter($pathFilterRegex)
            ->addAttributeToSort('entity_id')
            ->addAttributeToSort('parent_id')
            ->addAttributeToSort('position');


        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection  $collection */
        $collection = $this->categoryCollectionFactory->create();

        $collection->addAttributeToSelect(
            [
                'name',
                'is_active',
                'parent_id',
                'position',
                'include_in_menu'
            ]
        );
        //$collection
            //->addUrlRewriteToResult()
            //->addIsActiveFilter()
            //->addAttributeToFilter('level', ['gteq' => '2'])
            /*->addAttributeToSort('entity_id')
            ->addAttributeToSort('parent_id')
            ->addAttributeToSort('position')
            ->setPageSize(1000)
            ->setStoreId($this->storeManager->getStore()->getId())*/
        ;

        /*if (!$this->proxyConfigProvider->isManageAllCategories()) {
            $collection->addAttributeToFilter('hawk_landing_page', 1);
        }*/

        $pages = $categories->getLastPageNumber();
        $currentPage = 1;
        $cats = [];

        do {
            $categories->clear();
            $categories->setCurPage($currentPage);
            $categories->load();
            foreach ($categories as $cat) {
                $cats[] = [
                    'hawkurl' => sprintf("%s", $this->getRequestPath($cat)),
                    'name' => $cat->getName(),
                    'catid' => $cat->getId(),
                    'pid' => $cat->getParentId()
                ];
            }
            $currentPage++;
        } while ($currentPage <= $pages);

        return $cats;
    }

    /**
     * @param Category $category
     * @return string|null
     */
    public function getRequestPath(Category $category)
    {
        return str_replace($category->getUrlInstance()->getBaseUrl(), '', $category->getUrl());
    }

    /**
     * @param string $id
     * @return string|bool
     */
    private function getHawkNarrowXml($id)
    {
        $xml = simplexml_load_string(
            '<?xml version="1.0" encoding="UTF-8"?>
<Rule xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
RuleType="Group" Operator="All" />'
        );
        $rules = $xml->addChild('Rules');
        $rule = $rules->addChild('Rule');
        $rule->addAttribute('RuleType', 'Eval');
        $rule->addAttribute('Operator', 'None');
        $rule->addChild('Field', 'facet:category');
        $rule->addChild('Condition', 'is');
        $rule->addChild('Value', $id);
        $xml->addChild('Field');
        $xml->addChild('Condition');
        $xml->addChild('Value');
        return $xml->asXML();
    }
}
