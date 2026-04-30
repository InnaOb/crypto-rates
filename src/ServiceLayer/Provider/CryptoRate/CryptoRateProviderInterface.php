<?php

declare(strict_types=1);

namespace CryptoRate\ServiceLayer\Provider\CryptoRate;

use CryptoRate\DataLayer\Entity\CryptoRate;
use CryptoRate\Tool\Enum\Crypto\CurrencyPairEnum;
use DateTimeImmutable;

interface CryptoRateProviderInterface
{
    public function fetchAndSaveAll(): void;

    /**
     * @return CryptoRate[]
     */
    public function getLast24h(CurrencyPairEnum $pair): array;

    /**
     * @return CryptoRate[]
     */
    public function getByDay(CurrencyPairEnum $pair, DateTimeImmutable $date): array;
}
