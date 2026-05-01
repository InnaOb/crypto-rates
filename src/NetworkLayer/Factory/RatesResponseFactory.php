<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\Factory;

use CryptoRate\DataLayer\Collection\CryptoRateCollection;
use CryptoRate\DataLayer\Entity\CryptoRate;
use CryptoRate\NetworkLayer\DTO\Response\Rate\RateItemResponse;
use CryptoRate\NetworkLayer\DTO\Response\Rate\RatesData;
use CryptoRate\NetworkLayer\DTO\Response\Rate\RatesResponse;

final class RatesResponseFactory
{
    public function fromRates(CryptoRateCollection $rates): RatesResponse
    {
        return new RatesResponse(
            RatesData::fromItems(
                $rates->map(static fn(CryptoRate $rate) => new RateItemResponse(
                    pair: $rate->getPair(),
                    rate: $rate->getRate(),
                    recordedAt: $rate->getRecordedAt()->format(\DateTimeInterface::ATOM),
                ))
            )
        );
    }
}
