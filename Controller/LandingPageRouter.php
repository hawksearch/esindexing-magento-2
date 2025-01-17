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

namespace HawkSearch\EsIndexing\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class LandingPageRouter implements RouterInterface
{
    private ActionFactory $actionFactory;

    public function __construct(
        ActionFactory $actionFactory
    )
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function match(RequestInterface $request)
    {
        /*if (!$this->proxyConfigProvider->isLandingPageRouteEnabled()) {
            return false;
        }*/

        /*if (!$this->helper->isManagedLandingPage($request->getPathInfo())) {
            return false;
        }*/


        $path = $request->getOriginalPathInfo();

        if ($path != "/testlp.html") {
            return false;
        }
        $request->setModuleName('hawksearch')->setControllerName('landingPage')->setActionName('view');

        return $this->actionFactory->create(Forward::class);
    }
}
