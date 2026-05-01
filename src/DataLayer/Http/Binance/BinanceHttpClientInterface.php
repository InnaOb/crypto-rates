<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Http\Binance;

interface BinanceHttpClientInterface
{
    public function getPrice(string $symbol): string;
}
