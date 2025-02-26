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

namespace HawkSearch\EsIndexing\Gateway\Request;

use HawkSearch\Connector\Gateway\InstructionException;
use HawkSearch\Connector\Gateway\InstructionInterface;
use HawkSearch\Connector\Gateway\Request\BuilderInterface;
use HawkSearch\EsIndexing\Api\Data\LandingPageInterface;
use HawkSearch\EsIndexing\Model\LandingPage;

/**
 * @phpstan-import-type RequestSubject from InstructionInterface
 */
class LandingPageListBuilder implements BuilderInterface
{
    /**
     * Build request with Landing Pages collection
     *
     * @return RequestSubject
     * @throws InstructionException
     */
    public function build(array $buildSubject)
    {
        $data = [];
        /** @var LandingPage $landingPage */
        foreach ($buildSubject as $landingPage) {
            if (!$landingPage instanceof LandingPageInterface) {
                throw new InstructionException(__('Invalid request. Class: %1', self::class));
            }

            $data[] = $landingPage->__toArray();
        }
        return $data;
    }
}
