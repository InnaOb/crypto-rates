<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\DTO\Response\Rate;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[OA\Schema(description: 'Single cryptocurrency rate entry')]
final readonly class RateItemResponse
{
    public function __construct(
        #[OA\Property(description: 'Currency pair', example: 'EUR/BTC')]
        public string $pair,

        #[OA\Property(description: 'Exchange rate', example: '62345.12000000')]
        public string $rate,

        #[SerializedName('recorded_at')]
        #[OA\Property(property: 'recorded_at', description: 'Timestamp when rate was recorded', example: '2026-04-30T10:00:00+00:00')]
        public string $recordedAt,
    ) {
    }

    public function getPair(): string
    {
        return $this->pair;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function getRecordedAt(): string
    {
        return $this->recordedAt;
    }

}
