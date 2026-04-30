<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Http\Binance;

use CryptoRate\DataLayer\Http\BaseHttpClient;
use CryptoRate\DataLayer\Object\HttpClient\Request\BaseRequestOption;
use CryptoRate\FrameworkLayer\Exception\ServiceUnavailableCustomException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BinanceHttpClient extends BaseHttpClient implements BinanceHttpClientInterface
{
    private const PRICE_TICKER_PATH = '/api/v3/ticker/price';
    private const HTTP_CLIENT_NAME = 'BINANCE_HTTP_CLIENT';

    public function __construct(
        HttpClientInterface $binanceHttpClient,
        private readonly string $binanceBaseUri,
    ) {
        parent::__construct($binanceHttpClient, self::HTTP_CLIENT_NAME);
    }

    public function getPrice(string $symbol): string
    {
        $requestOption = BaseRequestOption::toGetRequestOption(
            baseUri: $this->binanceBaseUri,
            urlPath: self::PRICE_TICKER_PATH,
            query: ['symbol' => $symbol],
        );

        $this->sendRequest($requestOption);

        $decoded = json_decode($this->getResponseContent(), true);

        if (!isset($decoded['price'])) {
            throw new ServiceUnavailableCustomException(
                message: sprintf('[%s] Unexpected response format for symbol %s', self::HTTP_CLIENT_NAME, $symbol),
            );
        }

        return $decoded['price'];
    }
}
