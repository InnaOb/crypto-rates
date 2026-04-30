<?php

declare(strict_types=1);

namespace CryptoRate\Tests\Unit\ServiceLayer\Provider\CryptoRate;

use CryptoRate\DataLayer\Collection\CryptoRateCollection;
use CryptoRate\DataLayer\Entity\CryptoRate;
use CryptoRate\DataLayer\Http\Binance\BinanceHttpClientInterface;
use CryptoRate\DataLayer\Repository\CryptoRateRepositoryInterface;
use CryptoRate\FrameworkLayer\Exception\ServiceUnavailableCustomException;
use CryptoRate\ServiceLayer\Provider\CryptoRate\CryptoRateProvider;
use CryptoRate\Tool\Enum\Crypto\CurrencyPairEnum;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class CryptoRateProviderTest extends TestCase
{
    private BinanceHttpClientInterface&MockObject $binanceHttpClientMock;
    private CryptoRateRepositoryInterface&MockObject $cryptoRateRepositoryMock;

    private CryptoRateProvider $cryptoRateProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->binanceHttpClientMock = $this->createMock(BinanceHttpClientInterface::class);
        $this->cryptoRateRepositoryMock = $this->createMock(CryptoRateRepositoryInterface::class);

        $this->cryptoRateProvider = new CryptoRateProvider(
            $this->binanceHttpClientMock,
            $this->cryptoRateRepositoryMock,
        );

        $this->cryptoRateProvider->setLogger($this->createMock(LoggerInterface::class));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->binanceHttpClientMock,
            $this->cryptoRateRepositoryMock,
            $this->cryptoRateProvider,
        );
    }

    public function testFetchAllReturnsCryptoRateCollectionForEachPair(): void
    {
        $pairs = CurrencyPairEnum::cases();
        $prices = ['62345.12000000', '3120.45000000', '74.89000000'];

        $this->binanceHttpClientMock
            ->expects($this->exactly(count($pairs)))
            ->method('getPrice')
            ->willReturnOnConsecutiveCalls(...$prices);

        $result = $this->cryptoRateProvider->fetchAll();

        $this->assertInstanceOf(CryptoRateCollection::class, $result);
        $this->assertCount(count($pairs), $result);
    }

    public function testFetchAllReturnsCorrectPairKeys(): void
    {
        $this->binanceHttpClientMock
            ->method('getPrice')
            ->willReturn('50000.00000000');

        $result = $this->cryptoRateProvider->fetchAll();

        $pairs = array_map(static fn (CryptoRate $rate) => $rate->getPair(), iterator_to_array($result));

        $this->assertSame(['EUR/BTC', 'EUR/ETH', 'EUR/LTC'], $pairs);
    }

    public function testFetchAllSkipsFailingPairAndReturnsRest(): void
    {
        $pairs = CurrencyPairEnum::cases();

        $this->binanceHttpClientMock
            ->expects($this->exactly(count($pairs)))
            ->method('getPrice')
            ->willReturnCallback(static function (string $symbol): string {
                if ($symbol === CurrencyPairEnum::EUR_ETH->toBinanceSymbol()) {
                    throw new ServiceUnavailableCustomException('Binance timeout');
                }

                return '50000.00000000';
            });

        $result = $this->cryptoRateProvider->fetchAll();

        $this->assertCount(count($pairs) - 1, $result);
    }

    public function testFetchAllReturnsEmptyCollectionWhenAllPricesFail(): void
    {
        $this->binanceHttpClientMock
            ->method('getPrice')
            ->willThrowException(new ServiceUnavailableCustomException('Binance unavailable'));

        $result = $this->cryptoRateProvider->fetchAll();

        $this->assertTrue($result->isEmpty());
    }

    /**
     * @dataProvider \CryptoRate\Tests\DataProvider\Unit\ServiceLayer\Provider\CryptoRate\CryptoRateProviderDataProvider::providerGetLast24h
     */
    public function testGetLast24h(CurrencyPairEnum $pair, CryptoRateCollection $expected): void
    {
        $this->cryptoRateRepositoryMock
            ->expects($this->once())
            ->method('findLast24h')
            ->with($pair->toStorageKey())
            ->willReturn($expected);

        $result = $this->cryptoRateProvider->getLast24h($pair);

        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider \CryptoRate\Tests\DataProvider\Unit\ServiceLayer\Provider\CryptoRate\CryptoRateProviderDataProvider::providerGetByDay
     */
    public function testGetByDay(CurrencyPairEnum $pair, DateTimeImmutable $date, CryptoRateCollection $expected): void
    {
        $this->cryptoRateRepositoryMock
            ->expects($this->once())
            ->method('findByDay')
            ->with($pair->toStorageKey(), $date)
            ->willReturn($expected);

        $result = $this->cryptoRateProvider->getByDay($pair, $date);

        $this->assertSame($expected, $result);
    }
}
