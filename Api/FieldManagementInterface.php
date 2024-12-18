<?php
/**
 * Copyright (c) 2024 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Api;

use HawkSearch\EsIndexing\Api\Data\FieldInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface for managing Fields in HawkSearch
 *
 * @api
 * @since 0.8.0
 */
interface FieldManagementInterface
{
    /**
     * @deprecated
     * @see self::getFields()
     * @return FieldInterface[]
     */
    public function getHawkSearchFields(): array;

    /**
     * @return FieldInterface[]
     */
    public function getFields(): array;

    /**
     * @return FieldInterface
     * @throws CouldNotSaveException
     */
    public function addField(FieldInterface $field): FieldInterface;

    /**
     * @return FieldInterface
     * @throws CouldNotSaveException
     */
    public function updateField(FieldInterface $field): FieldInterface;
}
