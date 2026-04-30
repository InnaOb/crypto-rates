<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\DTO\Request\Rate;

use CryptoRate\NetworkLayer\DTO\Request\RequestDtoInterface;
use CryptoRate\Tool\Enum\Crypto\CurrencyPairEnum;
use CryptoRate\Tool\Enum\Validation\ValidationMessageEnum;
use CryptoRate\Tool\Helper\StringHelper;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DayRateRequest implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: ValidationMessageEnum::NOT_BLANK->value)]
        #[Assert\Choice(
            choices: ['EUR/BTC', 'EUR/ETH', 'EUR/LTC'],
            message: ValidationMessageEnum::INVALID_PAIR->value,
        )]
        public string $pair,

        #[Assert\NotBlank(message: ValidationMessageEnum::NOT_BLANK->value)]
        #[Assert\Date(message: ValidationMessageEnum::INVALID_DATE->value)]
        public string $date,
    ) {
    }

    public function getPair(): string
    {
        return $this->pair;
    }

    public function getPairEnum(): CurrencyPairEnum
    {
        return CurrencyPairEnum::from($this->pair);
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getDateAsImmutable(): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $this->date);
        assert($date !== false);

        return $date;
    }

    public static function fromQuery(array $query): self
    {
        return new self(
            pair: $query['pair'] ?? StringHelper::EMPTY_STRING,
            date: $query['date'] ?? StringHelper::EMPTY_STRING,
        );
    }
}
