<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Object\HttpClient\Request;

use CryptoRate\Tool\Enum\Http\RequestMethodEnum;
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

    public function getHttpOptionRequest(): mixed
    {
        $options = $this->httpOptions->toArray();

        return $options['query'] ?? $options['body'] ?? null;
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
