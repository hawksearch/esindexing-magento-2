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

use HawkSearch\Connector\Compatibility\PublicContractDeprecation;

PublicContractDeprecation::triggerInterfaceDeprecationMessage(
    AttributeHandlerInterface::class,
    '0.7.0',
    FieldHandlerInterface::class,
    'In favour of a new Field Handlers logic.'
);

/**
 * @deprecated 0.7.0 In favour of a new Field Handlers logic
 * @see FieldHandlerInterface
 * @phpstan-ignore-next-line
 */
interface AttributeHandlerInterface extends FieldHandlerInterface
{
}
