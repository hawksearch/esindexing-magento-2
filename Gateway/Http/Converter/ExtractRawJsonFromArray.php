<?php
/**
 * Copyright (c) 2025 Hawksearch (www.hawksearch.com) - All Rights Reserved
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

namespace HawkSearch\EsIndexing\Gateway\Http\Converter;

use HawkSearch\Connector\Gateway\Http\ConverterInterface;

class ExtractRawJsonFromArray implements ConverterInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function convert(mixed $data)
    {
        if (!is_array($data) && !isset($data['json'])) {
            throw new \InvalidArgumentException(__('$data argument is not an array or \'json\' key doesn\'t exist.')->render());
        }

        return (string)$data['json'];
    }
}
