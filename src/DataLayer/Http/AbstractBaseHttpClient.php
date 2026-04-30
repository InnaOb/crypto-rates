<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Http;

use CryptoRate\DataLayer\Object\HttpClient\Request\BaseRequestOption;
use CryptoRate\FrameworkLayer\Exception\ServiceUnavailableCustomException;
use CryptoRate\Tool\Helper\StringHelper;
use CryptoRate\Tool\Util\LoggerAwareTrait;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

abstract class AbstractBaseHttpClient
{
    use LoggerAwareTrait;

    public function __construct(
        protected readonly HttpClientInterface $httpClient,
        protected readonly string $httpClientName = 'BASE_HTTP_CLIENT',
    ) {
    }

    protected function prepareHttpClientResponse(BaseRequestOption $requestOption): ResponseInterface
    {
        $this->logHttpClientRequest(
            httpClientName: $this->httpClientName,
            requestOption: $requestOption,
            requestBody: $this->getRequestBodyString($requestOption),
        );

        try {
            return $this->httpClient->request(
                $requestOption->getRequestMethodString(),
                $requestOption->getUrlPath(),
                $requestOption->getHttpOptionArray(),
            );
        } catch (ExceptionInterface $exception) {
            throw new ServiceUnavailableCustomException(
                message: sprintf('[%s] Transport error: %s', $this->httpClientName, $exception->getMessage()),
                previous: $exception,
            );
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function awaitGetContent(ResponseInterface $httpClientResponse): string
    {
        $statusCode = $httpClientResponse->getStatusCode();

        if ($statusCode >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            $content = $httpClientResponse->getContent(false);
            if (empty($content)) {
                throw new ServerException($httpClientResponse);
            }
        } elseif ($statusCode >= Response::HTTP_BAD_REQUEST) {
            $content = $httpClientResponse->getContent(false);
            if (empty($content)) {
                throw new ClientException($httpClientResponse);
            }
        } else {
            $content = $httpClientResponse->getContent();
        }

        $this->logHttpClientResponse(
            httpClientName: $this->httpClientName,
            httpClientResponse: $httpClientResponse,
            responseContent: $content,
        );

        return $content;
    }

    private function getRequestBodyString(BaseRequestOption $requestOption): string
    {
        $body = $requestOption->getHttpOptionRequest();

        if (null === $body) {
            return StringHelper::EMPTY_STRING;
        }

        if (is_string($body)) {
            return $body;
        }

        try {
            return json_encode($body, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return StringHelper::EMPTY_STRING;
        }
    }
}
