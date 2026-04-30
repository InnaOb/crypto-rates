<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Http\Binance;

use CryptoRate\DataLayer\Http\BaseHttpClient;
use CryptoRate\DataLayer\Object\HttpClient\Request\BaseRequestOption;
use CryptoRate\DataLayer\Object\HttpClient\Response\Binance\BinancePriceResponse;
use CryptoRate\FrameworkLayer\Exception\ServiceUnavailableCustomException;
use CryptoRate\Tool\Enum\Monolog\ModuleContextEnum;
use CryptoRate\Tool\Util\SerializerAwareTrait;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BinanceHttpClient extends BaseHttpClient implements BinanceHttpClientInterface
{
    use SerializerAwareTrait;

    private const PRICE_TICKER_PATH = '/api/v3/ticker/price';

    public function __construct(
        HttpClientInterface $binanceHttpClient,
        private readonly string $binanceBaseUri,
    ) {
        parent::__construct($binanceHttpClient, ModuleContextEnum::BINANCE_HTTP_CLIENT->value);
    }

    public function getPrice(string $symbol): string
    {
        $requestOption = BaseRequestOption::toGetRequestOption(
            baseUri: $this->binanceBaseUri,
            urlPath: self::PRICE_TICKER_PATH,
            query: ['symbol' => $symbol],
        );

        $this->sendRequest($requestOption);

        try {
            $response = $this->serializer->deserialize(
                $this->getResponseContent(),
                BinancePriceResponse::class,
                'json',
            );

            return $response->price;
        } catch (SerializerExceptionInterface $e) {
            throw new ServiceUnavailableCustomException(
                message: sprintf('[%s] Unexpected response format for symbol %s', ModuleContextEnum::BINANCE_HTTP_CLIENT->value, $symbol),
                previous: $e,
            );
        }
    }
}
