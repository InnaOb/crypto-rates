<?php

declare(strict_types=1);

namespace CryptoRate\BusinessLogicLayer\Scheduler;

use CryptoRate\ServiceLayer\Provider\CryptoRate\CryptoRateProviderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class FetchCryptoRatesMessageHandler
{
    public function __construct(private CryptoRateProviderInterface $cryptoRateProvider)
    {
    }

    public function __invoke(FetchCryptoRatesMessage $message): void
    {
        $this->cryptoRateProvider->fetchAndSaveAll();
    }
}
