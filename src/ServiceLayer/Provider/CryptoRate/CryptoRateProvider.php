<?php

declare(strict_types=1);

namespace CryptoRate\ServiceLayer\Provider\CryptoRate;

use CryptoRate\DataLayer\Entity\CryptoRate;
use CryptoRate\DataLayer\Http\Binance\BinanceHttpClientInterface;
use CryptoRate\DataLayer\Repository\CryptoRateRepositoryInterface;
use CryptoRate\Tool\Enum\Crypto\CurrencyPairEnum;
use CryptoRate\Tool\Enum\Monolog\ModuleContextEnum;
use CryptoRate\Tool\Util\LoggerAwareTrait;
use DateTimeImmutable;

final class CryptoRateProvider implements CryptoRateProviderInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly BinanceHttpClientInterface $binanceHttpClient,
        private readonly CryptoRateRepositoryInterface $cryptoRateRepository,
    ) {
    }

    public function fetchAndSaveAll(): void
    {
        $this->logStarted(ModuleContextEnum::SCHEDULER);

        foreach (CurrencyPairEnum::cases() as $pair) {
            $this->cryptoRateRepository->save(
                new CryptoRate(
                    pair: $pair->toStorageKey(),
                    rate: $this->binanceHttpClient->getPrice($pair->toBinanceSymbol()),
                    recordedAt: new DateTimeImmutable(),
                )
            );
        }

        $this->logFinished(ModuleContextEnum::SCHEDULER);
    }

    /**
     * @return CryptoRate[]
     */
    public function getLast24h(CurrencyPairEnum $pair): array
    {
        return $this->cryptoRateRepository->findLast24h($pair->toStorageKey());
    }

    /**
     * @return CryptoRate[]
     */
    public function getByDay(CurrencyPairEnum $pair, DateTimeImmutable $date): array
    {
        return $this->cryptoRateRepository->findByDay($pair->toStorageKey(), $date);
    }
}
