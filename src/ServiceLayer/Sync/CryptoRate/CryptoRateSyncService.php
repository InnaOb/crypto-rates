<?php

declare(strict_types=1);

namespace CryptoRate\ServiceLayer\Sync\CryptoRate;

use CryptoRate\DataLayer\Repository\CryptoRateRepositoryInterface;
use CryptoRate\ServiceLayer\Provider\CryptoRate\CryptoRateProviderInterface;

final readonly class CryptoRateSyncService implements CryptoRateSyncServiceInterface
{
    public function __construct(
        private CryptoRateProviderInterface $cryptoRateProvider,
        private CryptoRateRepositoryInterface $cryptoRateRepository,
    ) {
    }

    public function sync(): void
    {
        $rates = $this->cryptoRateProvider->fetchAll();

        if ($rates->isEmpty()) {
            return;
        }

        $this->cryptoRateRepository->saveAll($rates);
    }
}
