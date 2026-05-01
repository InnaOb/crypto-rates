<?php

declare(strict_types=1);

namespace CryptoRate\Tests\Unit\DataLayer\Http\Binance;

use CryptoRate\DataLayer\Http\Binance\BinanceHttpClient;
use CryptoRate\DataLayer\Http\Binance\BinanceHttpClientInterface;
use CryptoRate\FrameworkLayer\Exception\ServiceUnavailableCustomException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @internal
 */
final class BinanceHttpClientTest extends TestCase
{
    private const BINANCE_BASE_URI = 'https://api.binance.com';

    private MockObject $httpClientMock;
    private MockObject $httpResponseMock;

    private BinanceHttpClientInterface $binanceHttpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->httpResponseMock = $this->createMock(ResponseInterface::class);

        $this->binanceHttpClient = new BinanceHttpClient(
            $this->httpClientMock,
            self::BINANCE_BASE_URI,
        );

        $this->binanceHttpClient->setLogger($this->createMock(LoggerInterface::class));
        $this->binanceHttpClient->setSerializer(new Serializer(
            [new ObjectNormalizer(propertyTypeExtractor: new ReflectionExtractor())],
            [new JsonEncoder()],
        ));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->httpClientMock,
            $this->httpResponseMock,
            $this->binanceHttpClient,
        );
    }

    /**
     * @dataProvider \CryptoRate\Tests\DataProvider\Unit\DataLayer\Http\Binance\BinanceHttpClientDataProvider::providerGetPrice
     */
    public function testGetPrice(
        string $symbol,
        int $responseStatus,
        string $responseContent,
        ?string $expectedPrice,
        ?string $expectedException,
    ): void {
        $this->httpResponseMock->method('getStatusCode')->willReturn($responseStatus);
        $this->httpResponseMock->method('getContent')->willReturn($responseContent);
        $this->httpClientMock->method('request')->willReturn($this->httpResponseMock);

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $actualPrice = $this->binanceHttpClient->getPrice($symbol);

        if ($expectedPrice !== null) {
            $this->assertSame($expectedPrice, $actualPrice);
        }
    }

    public function testGetPriceThrowsWhenHttpClientThrowsTransportException(): void
    {
        $this->httpClientMock
            ->method('request')
            ->willThrowException(new TransportException('Connection refused'));

        $this->expectException(ServiceUnavailableCustomException::class);

        $this->binanceHttpClient->getPrice('BTCEUR');
    }

    public function testGetPriceCallsCorrectEndpoint(): void
    {
        $this->httpResponseMock->method('getStatusCode')->willReturn(200);
        $this->httpResponseMock->method('getContent')->willReturn(
            json_encode(['symbol' => 'BTCEUR', 'price' => '50000.00000000'])
        );

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                '/api/v3/ticker/price',
                $this->callback(static fn(array $options): bool => ($options['query']['symbol'] ?? null) === 'BTCEUR'),
            )
            ->willReturn($this->httpResponseMock);

        $price = $this->binanceHttpClient->getPrice('BTCEUR');

        $this->assertSame('50000.00000000', $price);
    }
}
