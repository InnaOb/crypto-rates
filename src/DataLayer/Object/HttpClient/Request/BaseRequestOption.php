<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Object\HttpClient\Request;

use CryptoRate\Tool\Enum\Http\RequestMethodEnum;
use CryptoRate\Tool\Helper\StringHelper;
use Symfony\Component\HttpClient\HttpOptions;

final class BaseRequestOption
{
    private HttpOptions $httpOptions;

    public function __construct(
        private readonly RequestMethodEnum $requestMethod,
        private readonly string $baseUri,
        private readonly string $urlPath,
    ) {
        $this->httpOptions = new HttpOptions();
    }

    public function getRequestMethodString(): string
    {
        return $this->requestMethod->value;
    }

    public function getUrlPath(): string
    {
        return $this->urlPath;
    }

    public function getFullUrl(): string
    {
        return sprintf('%s%s', $this->baseUri, $this->urlPath);
    }

    public function setQuery(array $query): self
    {
        $this->httpOptions->setQuery($query);

        return $this;
    }

    public function getHttpOptionArray(): array
    {
        return $this->httpOptions->toArray();
    }

    public function getRequestBodyAsString(): string
    {
        $options = $this->httpOptions->toArray();
        $body = $options['query'] ?? $options['body'] ?? null;

        if (null === $body) {
            return StringHelper::EMPTY_STRING;
        }

        if (is_string($body)) {
            return $body;
        }

        try {
            return json_encode($body, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return StringHelper::EMPTY_STRING;
        }
    }

    public static function toGetRequestOption(
        string $baseUri,
        string $urlPath,
        ?array $query = null,
    ): self {
        $option = new self(
            requestMethod: RequestMethodEnum::GET,
            baseUri: $baseUri,
            urlPath: $urlPath,
        );

        if (null !== $query) {
            $option->setQuery($query);
        }

        return $option;
    }
}
