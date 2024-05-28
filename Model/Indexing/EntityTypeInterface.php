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

namespace HawkSearch\EsIndexing\Model\Indexing;


/**
 * @method FieldHandlerInterface getFieldHandler() Use this method in your class implementations for smooth transitions
 *          since 0.9.0
 */
interface EntityTypeInterface
{
    /**
     * @return string
     */
    public function getTypeName() : string;

    /**
     * @param string $type
     * @return $this
     */
    public function setTypeName(string $type);

    /**
     * @param string $itemId
     * @return string
     */
    public function getUniqueId(string $itemId);

    /**
     * @return EntityRebuildInterface
     */
    public function getRebuilder() : EntityRebuildInterface;

    /**
     * @return ItemsDataProviderInterface
     */
    public function getItemsDataProvider() : ItemsDataProviderInterface;

    /**
     * @return ItemsIndexerInterface
     */
    public function getItemsIndexer() : ItemsIndexerInterface;

    /**
     * @deprecated 0.7.0 in favour of a new Field Handlers logic
     * @see self::getFieldHandler()
     * @return AttributeHandlerInterface|FieldHandlerInterface
     */
    public function getAttributeHandler() : FieldHandlerInterface;

    /**
     * @return FieldHandlerInterface
     */
    /*public function getFieldHandler() : FieldHandlerInterface;*/

    /**
     * @return AbstractConfigHelper
     */
    public function getConfigHelper() : AbstractConfigHelper;
}
