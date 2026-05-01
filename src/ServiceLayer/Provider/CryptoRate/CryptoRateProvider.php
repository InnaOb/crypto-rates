<?php

declare(strict_types=1);

namespace CryptoRate\ServiceLayer\Provider\CryptoRate;

use CryptoRate\DataLayer\Collection\CryptoRateCollection;
use CryptoRate\DataLayer\Entity\CryptoRate;
use CryptoRate\DataLayer\Http\Binance\BinanceHttpClientInterface;
use CryptoRate\DataLayer\Repository\CryptoRateRepositoryInterface;
use CryptoRate\FrameworkLayer\Exception\ServiceUnavailableCustomException;
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

    public function fetchAll(): CryptoRateCollection
    {
        $this->logStarted(ModuleContextEnum::SCHEDULER);

        $rates = CryptoRateCollection::empty();

        foreach (CurrencyPairEnum::cases() as $pair) {
            try {
                $price = $this->binanceHttpClient->getPrice($pair->toBinanceSymbol());
            } catch (ServiceUnavailableCustomException $e) {
                $this->logError($e, ModuleContextEnum::SCHEDULER, ['pair' => $pair->value]);
                continue;
            }

            $rates = $rates->withAdded(
                new CryptoRate(
                    pair: $pair->toStorageKey(),
                    rate: $price,
                    recordedAt: new DateTimeImmutable(),
                )
            );
        }

        $this->logFinished(ModuleContextEnum::SCHEDULER);

        return $rates;
    }

    public function getLast24h(CurrencyPairEnum $pair): CryptoRateCollection
    {
        return $this->cryptoRateRepository->findLast24h($pair->toStorageKey());
    }

    public function getByDay(CurrencyPairEnum $pair, DateTimeImmutable $date): CryptoRateCollection
    {
        return $this->cryptoRateRepository->findByDay($pair->toStorageKey(), $date);
    }
}
