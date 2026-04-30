<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Repository;

use CryptoRate\DataLayer\Collection\CryptoRateCollection;
use CryptoRate\DataLayer\Entity\CryptoRate;
use DateTimeImmutable;

interface CryptoRateRepositoryInterface
{
    public function save(CryptoRate $cryptoRate): void;

    public function findLast24h(string $pair): CryptoRateCollection;

    public function findByDay(string $pair, DateTimeImmutable $date): CryptoRateCollection;
}
