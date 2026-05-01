<?php

declare(strict_types=1);

namespace CryptoRate\Tests\DataProvider\Unit\DataLayer\Http\Binance;

use CryptoRate\FrameworkLayer\Exception\ServiceUnavailableCustomException;
use Iterator;

final class BinanceHttpClientDataProvider
{
    public static function providerGetPrice(): Iterator
    {
        yield 'successful BTCEUR price response' => [
            'symbol'          => 'BTCEUR',
            'responseStatus'  => 200,
            'responseContent' => json_encode(['symbol' => 'BTCEUR', 'price' => '62345.12000000']),
            'expectedPrice'   => '62345.12000000',
            'expectedException' => null,
        ];

        yield 'successful ETHEUR price response' => [
            'symbol'          => 'ETHEUR',
            'responseStatus'  => 200,
            'responseContent' => json_encode(['symbol' => 'ETHEUR', 'price' => '3120.45000000']),
            'expectedPrice'   => '3120.45000000',
            'expectedException' => null,
        ];

        yield 'successful LTCEUR price response' => [
            'symbol'          => 'LTCEUR',
            'responseStatus'  => 200,
            'responseContent' => json_encode(['symbol' => 'LTCEUR', 'price' => '74.89000000']),
            'expectedPrice'   => '74.89000000',
            'expectedException' => null,
        ];

        yield 'response missing price key throws ServiceUnavailableCustomException' => [
            'symbol'          => 'BTCEUR',
            'responseStatus'  => 200,
            'responseContent' => json_encode(['symbol' => 'BTCEUR']),
            'expectedPrice'   => null,
            'expectedException' => ServiceUnavailableCustomException::class,
        ];

        yield 'empty JSON response throws ServiceUnavailableCustomException' => [
            'symbol'          => 'BTCEUR',
            'responseStatus'  => 200,
            'responseContent' => json_encode([]),
            'expectedPrice'   => null,
            'expectedException' => ServiceUnavailableCustomException::class,
        ];
    }
}
