<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Http;

use CryptoRate\DataLayer\Object\HttpClient\Request\BaseRequestOption;
use CryptoRate\FrameworkLayer\Exception\ServiceUnavailableCustomException;
use CryptoRate\Tool\Helper\StringHelper;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

class BaseHttpClient extends AbstractBaseHttpClient
{
    private string $responseContent = StringHelper::EMPTY_STRING;

    public function getResponseContent(): string
    {
        return $this->responseContent;
    }

    public function sendRequest(BaseRequestOption $requestOption): void
    {
        try {
            $httpClientResponse = $this->prepareHttpClientResponse($requestOption);
            $this->responseContent = $this->awaitGetContent($httpClientResponse);
        } catch (ExceptionInterface $exception) {
            if ($exception instanceof HttpExceptionInterface) {
                $this->logHttpClientResponse(
                    httpClientName: $this->httpClientName,
                    httpClientResponse: $exception->getResponse(),
                );
            }

            throw new ServiceUnavailableCustomException(
                message: sprintf('[%s] Request failed: %s', $this->httpClientName, $exception->getMessage()),
                previous: $exception,
            );
        }
    }
}
