<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\Factory;

use CryptoRate\DataLayer\Entity\CryptoRate;
use CryptoRate\NetworkLayer\DTO\Response\Rate\RateItemResponse;
use CryptoRate\NetworkLayer\DTO\Response\Rate\RatesData;
use CryptoRate\NetworkLayer\DTO\Response\Rate\RatesResponse;

final class RatesResponseFactory
{
    /**
     * @param CryptoRate[] $rates
     */
    public function fromRates(array $rates): RatesResponse
    {
        return new RatesResponse(
            RatesData::fromItems(
                array_map(static fn(CryptoRate $rate) => RateItemResponse::fromEntity($rate), $rates)
            )
        );
    }
}
