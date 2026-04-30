<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Object\HttpClient\Response\Binance;

final readonly class BinancePriceResponse
{
    public function __construct(
        public string $symbol,
        public string $price,
    ) {
    }
}
