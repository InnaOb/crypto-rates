<?php

declare(strict_types=1);

namespace CryptoRate\Tests\DataProvider\Unit\ServiceLayer\Provider\CryptoRate;

use CryptoRate\DataLayer\Collection\CryptoRateCollection;
use CryptoRate\DataLayer\Entity\CryptoRate;
use CryptoRate\Tool\Enum\Crypto\CurrencyPairEnum;
use DateTimeImmutable;
use Generator;

final class CryptoRateProviderDataProvider
{
    public static function providerGetLast24h(): Generator
    {
        $recordedAt = new DateTimeImmutable('2026-04-30 10:00:00');

        yield 'returns rates for EUR/BTC pair' => [
            'pair'     => CurrencyPairEnum::EUR_BTC,
            'expected' => CryptoRateCollection::fromEntities([
                new CryptoRate('EUR/BTC', '62345.12000000', $recordedAt),
                new CryptoRate('EUR/BTC', '62400.00000000', $recordedAt->modify('+5 minutes')),
            ]),
        ];

        yield 'returns empty collection when no rates stored' => [
            'pair'     => CurrencyPairEnum::EUR_ETH,
            'expected' => CryptoRateCollection::fromEntities([]),
        ];
    }

    public static function providerGetByDay(): Generator
    {
        $date = new DateTimeImmutable('2026-04-29');
        $recordedAt = new DateTimeImmutable('2026-04-29 08:00:00');

        yield 'returns rates for EUR/LTC on a given day' => [
            'pair'     => CurrencyPairEnum::EUR_LTC,
            'date'     => $date,
            'expected' => CryptoRateCollection::fromEntities([
                new CryptoRate('EUR/LTC', '74.89000000', $recordedAt),
            ]),
        ];

        yield 'returns empty collection when no rates for that day' => [
            'pair'     => CurrencyPairEnum::EUR_BTC,
            'date'     => new DateTimeImmutable('2020-01-01'),
            'expected' => CryptoRateCollection::fromEntities([]),
        ];
    }
}
