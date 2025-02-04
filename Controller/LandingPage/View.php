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

namespace HawkSearch\EsIndexing\Controller\LandingPage;

use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;
use HawkSearch\EsIndexing\Api\Data\LandingPageInterfaceFactory;
use HawkSearch\EsIndexing\Api\LandingPageManagementInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Framework\App\Action\Action
{
    private PageFactory $resultPageFactory;
    private Session $session;
    private Registry $coreRegistry;
    private CategoryFactory $categoryFactory;
    private LandingPageManagementInterface $landingPageManagement;
    private LandingPageInterfaceFactory $landingPageInterfaceFactory;

    public function __construct(
        Context $context,
        Session $session,
        Registry $coreRegistry,
        CategoryFactory $categoryFactory,
        PageFactory $resultPageFactory,
        LandingPageManagementInterface $landingPageManagement,
        LandingPageInterfaceFactory $landingPageInterfaceFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        $this->coreRegistry = $coreRegistry;
        $this->categoryFactory = $categoryFactory;
        $this->landingPageManagement = $landingPageManagement;
        $this->landingPageInterfaceFactory = $landingPageInterfaceFactory;
    }

    public function execute(): ResultInterface
    {
        $category = $this->categoryFactory->create();

        //@TODO refactor this
        $category->setData('hawksearch_landing_page', true);

        //@TODO I am not sure if this is needed
        $category->setData('hawksearch_breadcrumb_path',
            [0 => [
                'label' => $category->getName(),
                'link' => ''
            ]]
        );

        $this->coreRegistry->register('current_category', $category);

        $page = $this->resultPageFactory->create();

        //@TODO I am not sure if this is needed
        $page->getConfig()->addBodyClass('page-products');


        //$res = $this->landingPageManagement->getLandingPages();
        //$res = $this->landingPageManagement->getLandingPageUrls();

        $landingPages = [];

        /** @var LandingPageInterface $lp */
        /*$lp = $this->landingPageInterfaceFactory->create();

        $lp->setName('testName111')
            ->setCustomUrl('testname111.html')
            ->setIsFacetOverride(false)
            ->setPageType('ProductListing')
            ;
        $landingPages[] = $lp;

        $landingPages[] = $this->landingPageInterfaceFactory->create()
            ->setName('testName222')
            ->setCustomUrl('testname222.html')
            ->setIsFacetOverride(false)
            ->setPageType('ProductListing');
        $res = $this->landingPageManagement->addLandingPages($landingPages);*/

        /*$landingPageIds = ['5968059', '5968060'];
        $res = $this->landingPageManagement->deleteLandingPages($landingPageIds);*/


        $landingPages[] = $this->landingPageInterfaceFactory->create()
            ->setName('testName222 edited')
            ->setCustomUrl('testname222.html')
            ->setIsFacetOverride(false)
            ->setPageType('ProductListing')
            ->setPageId(5978900);
        $this->landingPageManagement->updateLandingPages($landingPages);
        return $page;
    }
}
