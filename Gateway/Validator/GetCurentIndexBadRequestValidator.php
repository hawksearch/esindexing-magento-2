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

namespace HawkSearch\EsIndexing\Gateway\Validator;

use HawkSearch\Connector\Gateway\Validator\ResultInterface;

class GetCurentIndexBadRequestValidator extends BadRequestValidator
{
    private const NO_INDEX_MESSAGE = "Unable to retrieve the current index name.";

    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $result = parent::validate($validationSubject);

        if (!$result->isValid()) {
            $message = current($result->getFailsDescription());
            if ($message == self::NO_INDEX_MESSAGE) {
                $result = $this->createResult(true);
            }
        }

        return $result;
    }
}
