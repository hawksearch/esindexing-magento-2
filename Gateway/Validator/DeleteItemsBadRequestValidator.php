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

namespace HawkSearch\EsIndexing\Gateway\Validator;

use HawkSearch\Connector\Gateway\Helper\HttpResponseReader;
use HawkSearch\Connector\Gateway\Helper\SubjectReader;
use HawkSearch\Connector\Gateway\Http\ClientInterface;
use HawkSearch\Connector\Gateway\Validator\AbstractValidator;
use HawkSearch\Connector\Gateway\Validator\ResultInterface;
use HawkSearch\Connector\Gateway\Validator\ResultInterfaceFactory;

class DeleteItemsBadRequestValidator extends AbstractValidator
{
    /**
     * @var HttpResponseReader
     */
    private HttpResponseReader $httpResponseReader;

    /**
     * @var SubjectReader
     */
    private SubjectReader $subjectReader;

    public function __construct(
        ResultInterfaceFactory $resultFactory,
        HttpResponseReader $httpResponseReader,
        SubjectReader $subjectReader
    )
    {
        parent::__construct($resultFactory);
        $this->httpResponseReader = $httpResponseReader;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $response = $this->subjectReader->readResponse($validationSubject);
        $responseCode = $this->httpResponseReader->readResponseCode($response);

        $errors = [];
        if ($responseCode == 400) {
            if (isset($response[ClientInterface::RESPONSE_DATA]['Status'])
                && $response[ClientInterface::RESPONSE_DATA]['Status'] === 'Failed') {
                $items = $response[ClientInterface::RESPONSE_DATA]['Items'] ?? [];

                $noMessageFound = false;
                //Skip errors if Item was not found
                foreach ($items as $item) {
                    if (isset($item['Status']) && $item['Status'] === 'Failed'
                        && isset($item['Message']) && $item['Message'] === 'Item not found') {
                        continue;
                    } elseif (isset($item['Message']) && $item['Message']) {
                        $errors[] = $item['Message'];
                    } else {
                        $noMessageFound = true;
                    }
                }

                if (!$errors && $noMessageFound) {
                    $errors[] = __('Bad Request');
                }
            }

            return $this->createResult(
                !$errors,
                $errors
            );
        }

        return $this->createResult(true);
    }
}
