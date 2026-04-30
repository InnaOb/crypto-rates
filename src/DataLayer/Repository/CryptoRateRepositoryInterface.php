<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Repository;

use CryptoRate\DataLayer\Entity\CryptoRate;
use DateTimeImmutable;

interface CryptoRateRepositoryInterface
{
    public function save(CryptoRate $cryptoRate): void;

    /**
     * @return CryptoRate[]
     */
    public function findLast24h(string $pair): array;

    /**
     * @return CryptoRate[]
     */
    public function findByDay(string $pair, DateTimeImmutable $date): array;
}
