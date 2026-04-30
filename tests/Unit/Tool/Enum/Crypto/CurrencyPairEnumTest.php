<?php

declare(strict_types=1);

namespace CryptoRate\Tests\Unit\Tool\Enum\Crypto;

use CryptoRate\Tool\Enum\Crypto\CurrencyPairEnum;
use Iterator;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @internal
 */
final class CurrencyPairEnumTest extends TestCase
{
    /**
     * @dataProvider providerToBinanceSymbol
     */
    public function testToBinanceSymbol(CurrencyPairEnum $pair, string $expectedSymbol): void
    {
        $this->assertSame($expectedSymbol, $pair->toBinanceSymbol());
    }

    public static function providerToBinanceSymbol(): Iterator
    {
        yield 'EUR/BTC maps to BTCEUR' => [CurrencyPairEnum::EUR_BTC, 'BTCEUR'];
        yield 'EUR/ETH maps to ETHEUR' => [CurrencyPairEnum::EUR_ETH, 'ETHEUR'];
        yield 'EUR/LTC maps to LTCEUR' => [CurrencyPairEnum::EUR_LTC, 'LTCEUR'];
    }

    /**
     * @dataProvider providerToStorageKey
     */
    public function testToStorageKey(CurrencyPairEnum $pair, string $expectedKey): void
    {
        $this->assertSame($expectedKey, $pair->toStorageKey());
    }

    public static function providerToStorageKey(): Iterator
    {
        yield 'EUR/BTC storage key' => [CurrencyPairEnum::EUR_BTC, 'EUR/BTC'];
        yield 'EUR/ETH storage key' => [CurrencyPairEnum::EUR_ETH, 'EUR/ETH'];
        yield 'EUR/LTC storage key' => [CurrencyPairEnum::EUR_LTC, 'EUR/LTC'];
    }

    /**
     * @dataProvider providerFromBinanceSymbol
     */
    public function testFromBinanceSymbol(string $symbol, CurrencyPairEnum $expectedPair): void
    {
        $this->assertSame($expectedPair, CurrencyPairEnum::fromBinanceSymbol($symbol));
    }

    public static function providerFromBinanceSymbol(): Iterator
    {
        yield 'BTCEUR resolves to EUR_BTC' => ['BTCEUR', CurrencyPairEnum::EUR_BTC];
        yield 'ETHEUR resolves to EUR_ETH' => ['ETHEUR', CurrencyPairEnum::EUR_ETH];
        yield 'LTCEUR resolves to EUR_LTC' => ['LTCEUR', CurrencyPairEnum::EUR_LTC];
    }

    public function testFromBinanceSymbolThrowsOnUnknownSymbol(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('Unknown Binance symbol: XYZEUR');

        CurrencyPairEnum::fromBinanceSymbol('XYZEUR');
    }

    public function testAllCasesHaveUniqueStorageKeys(): void
    {
        $keys = array_map(static fn(CurrencyPairEnum $p): string => $p->toStorageKey(), CurrencyPairEnum::cases());

        $this->assertCount(count(CurrencyPairEnum::cases()), array_unique($keys));
    }

    public function testAllCasesHaveUniqueBinanceSymbols(): void
    {
        $symbols = array_map(static fn(CurrencyPairEnum $p): string => $p->toBinanceSymbol(), CurrencyPairEnum::cases());

        $this->assertCount(count(CurrencyPairEnum::cases()), array_unique($symbols));
    }

    public function testRoundTripFromBinanceSymbol(): void
    {
        foreach (CurrencyPairEnum::cases() as $pair) {
            $this->assertSame($pair, CurrencyPairEnum::fromBinanceSymbol($pair->toBinanceSymbol()));
        }
    }
}
