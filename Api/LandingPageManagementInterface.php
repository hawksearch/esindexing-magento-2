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
namespace HawkSearch\EsIndexing\Api;

use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;

/**
 * Interface for managing Landing Pages in HawkSearch
 *
 * @api
 * @since 0.8.0
 */
interface LandingPageManagementInterface
{
    /**
     * @return LandingPageInterface[]
     */
    public function getLandingPages();

    /**
     * @return array
     */
    public function getLandingPageUrls();

    /**
     * @param LandingPageInterface[] $landingPages
     * @return void
     */
    public function addLandingPages(array $landingPages);

    /**
     * @param LandingPageInterface[] $landingPages
     * @return void
     */
    public function updateLandingPages(array $landingPages);

    /**
     * @param array $landingPageIds
     * @param bool $safeDelete Checks if Landing pages exist before deletion
     * @return void
     */
    public function deleteLandingPages(array $landingPageIds, bool $safeDelete = false);
}
