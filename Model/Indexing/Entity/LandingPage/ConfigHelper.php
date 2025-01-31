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

namespace HawkSearch\EsIndexing\Model\Indexing\Entity\LandingPage;

use HawkSearch\EsIndexing\Model\Config\Indexing as IndexingConfig;
use HawkSearch\EsIndexing\Model\Indexing\AbstractConfigHelper;

class ConfigHelper extends AbstractConfigHelper
{
    private ?int $batchSize;

    public function __construct(
        IndexingConfig $indexingConfig,
        /** @todo change type: int -> ?int */
        int $batchSize = null
    )
    {
        parent::__construct($indexingConfig);
        $this->batchSize = $batchSize;
    }

    public function isEnabled($store = null)
    {
        //@todo check if indexing of landing page entity is allowed
        return parent::isEnabled($store);
    }

    public function getBatchSize($store = null)
    {
        return $this->batchSize ?? parent::getBatchSize($store);
    }
}
