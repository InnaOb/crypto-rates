<?php

declare(strict_types=1);

namespace CryptoRate\Tests\Unit\ServiceLayer\Provider\CryptoRate;

use CryptoRate\DataLayer\Entity\CryptoRate;
use CryptoRate\DataLayer\Http\Binance\BinanceHttpClientInterface;
use CryptoRate\DataLayer\Repository\CryptoRateRepositoryInterface;
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

    public function testFetchAndSaveAllFetchesPriceForEachPairAndSaves(): void
    {
        $pairs = CurrencyPairEnum::cases();
        $prices = ['62345.12000000', '3120.45000000', '74.89000000'];

        $this->binanceHttpClientMock
            ->expects($this->exactly(count($pairs)))
            ->method('getPrice')
            ->willReturnOnConsecutiveCalls(...$prices);

        $this->cryptoRateRepositoryMock
            ->expects($this->exactly(count($pairs)))
            ->method('save')
            ->with($this->isInstanceOf(CryptoRate::class));

        $this->cryptoRateProvider->fetchAndSaveAll();
    }

    public function testFetchAndSaveAllSavesCorrectPairKeys(): void
    {
        $savedPairs = [];

        $this->binanceHttpClientMock
            ->method('getPrice')
            ->willReturn('50000.00000000');

        $this->cryptoRateRepositoryMock
            ->method('save')
            ->willReturnCallback(static function (CryptoRate $rate) use (&$savedPairs): void {
                $savedPairs[] = $rate->getPair();
            });

        $this->cryptoRateProvider->fetchAndSaveAll();

        $this->assertSame(['EUR/BTC', 'EUR/ETH', 'EUR/LTC'], $savedPairs);
    }

    /**
     * @dataProvider \CryptoRate\Tests\DataProvider\Unit\ServiceLayer\Provider\CryptoRate\CryptoRateProviderDataProvider::providerGetLast24h
     *
     * @param CryptoRate[] $expected
     */
    public function testGetLast24h(CurrencyPairEnum $pair, array $expected): void
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
     *
     * @param CryptoRate[] $expected
     */
    public function testGetByDay(CurrencyPairEnum $pair, DateTimeImmutable $date, array $expected): void
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
