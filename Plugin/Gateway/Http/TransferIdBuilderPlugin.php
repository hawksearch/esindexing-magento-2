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

namespace HawkSearch\EsIndexing\Plugin\Gateway\Http;

use HawkSearch\Connector\Gateway\Http\TransferInterface;

class TransferIdBuilderPlugin
{
    /**
     * @return string
     */
    public function afterGetUri(TransferInterface $subject, string $result): string
    {
        $openCurlyBracket = str_replace('%', '\%', rawurlencode('{'));
        $closeCurlyBracket = str_replace('%', '\%', rawurlencode('}'));
        //Url encoded representation of "/{{\w+}}/" template
        $matchTemplate = "/$openCurlyBracket$openCurlyBracket(\w+)$closeCurlyBracket$closeCurlyBracket/";

        $matches = [];
        if (preg_match($matchTemplate, $result, $matches)) {
            $search = $matches[0];
            $param = $matches[1];

            $body = $subject->getBody();
            if (is_array($body) && isset($body[$param])) {
                $result = str_replace($search, (string)$body[$param], $result);
            }
        }

        return $result;
    }
}
