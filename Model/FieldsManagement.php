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

namespace HawkSearch\EsIndexing\Model;

use HawkSearch\Connector\Gateway\Instruction\InstructionManagerPool;
use HawkSearch\Connector\Gateway\InstructionException;
use HawkSearch\EsIndexing\Api\FieldsManagementInterface;
use Magento\Framework\Exception\NotFoundException;

class FieldsManagement implements FieldsManagementInterface
{
    /**
     * @var InstructionManagerPool
     */
    private $instructionManagerPool;

    /**
     * FieldsManagement constructor.
     *
     * @param InstructionManagerPool $instructionManagerPool
     */
    public function __construct(
        InstructionManagerPool $instructionManagerPool
    ) {
        $this->instructionManagerPool = $instructionManagerPool;
    }

    /**
     * @inheritDoc
     * @throws InstructionException
     * @throws NotFoundException
     */
    public function getHawkSearchFields()
    {
        return $this->instructionManagerPool->get('hawksearch')
            ->executeByCode('getFields')->get();
    }
}
