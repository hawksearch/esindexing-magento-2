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

namespace HawkSearch\EsIndexing\Model\ResourceModel\AsynchronousOperations;

use Magento\AsynchronousOperations\Model\ResourceModel\Operation as OperationResource;

class Operation
{
    private OperationResource $operationResource;

    /**
     * @param OperationResource $operationResource
     */
    public function __construct(OperationResource $operationResource)
    {
        $this->operationResource = $operationResource;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isStartedAtColumnExists()
    {
        return $this->operationResource->getConnection()->tableColumnExists(
            $this->operationResource->getMainTable(),
            'started_at'
        );
    }
}
