<?php

declare(strict_types=1);

namespace CryptoRate\BusinessLogicLayer\Scheduler;

use CryptoRate\ServiceLayer\Sync\CryptoRate\CryptoRateSyncServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class FetchCryptoRatesMessageHandler
{
    public function __construct(private CryptoRateSyncServiceInterface $cryptoRateSyncService)
    {
    }

    public function __invoke(FetchCryptoRatesMessage $message): void
    {
        $this->cryptoRateSyncService->sync();
    }
}
