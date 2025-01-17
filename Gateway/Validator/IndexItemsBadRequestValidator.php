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

class IndexItemsBadRequestValidator extends AbstractValidator
{
    private HttpResponseReader $httpResponseReader;
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
                && $response[ClientInterface::RESPONSE_DATA]['Status'] !== 'Success') {
                $items = $response[ClientInterface::RESPONSE_DATA]['Items'] ?? [];

                $errors[] = __('Bad Request');
                foreach ($items as $item) {
                    if (isset($item['Message'])) {
                        $errors[] = $item['Message'];
                    }
                }
            } elseif (!is_array($response[ClientInterface::RESPONSE_DATA])
                && !empty($response[ClientInterface::RESPONSE_DATA])
            ) {
                $errors[] = (string)$response[ClientInterface::RESPONSE_DATA];
            }

            return $this->createResult(
                false,
                $errors
            );
        }

        return $this->createResult(true);
    }
}
