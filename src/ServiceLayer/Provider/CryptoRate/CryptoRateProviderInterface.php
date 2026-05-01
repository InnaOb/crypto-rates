<?php

declare(strict_types=1);

namespace CryptoRate\ServiceLayer\Provider\CryptoRate;

use CryptoRate\DataLayer\Collection\CryptoRateCollection;
use CryptoRate\Tool\Enum\Crypto\CurrencyPairEnum;
use DateTimeImmutable;

interface CryptoRateProviderInterface
{
    public function fetchAll(): CryptoRateCollection;

    public function getLast24h(CurrencyPairEnum $pair): CryptoRateCollection;

    public function getByDay(CurrencyPairEnum $pair, DateTimeImmutable $date): CryptoRateCollection;
}
