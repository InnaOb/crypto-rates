<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\DTO\Request\Rate;

use CryptoRate\NetworkLayer\DTO\Request\RequestDtoInterface;
use CryptoRate\Tool\Enum\Crypto\CurrencyPairEnum;
use CryptoRate\Tool\Enum\Validation\ValidationMessageEnum;
use CryptoRate\Tool\Helper\StringHelper;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Last24hRequest implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: ValidationMessageEnum::NOT_BLANK->value)]
        #[Assert\Choice(
            choices: ['EUR/BTC', 'EUR/ETH', 'EUR/LTC'],
            message: ValidationMessageEnum::INVALID_PAIR->value,
        )]
        public string $pair,
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

    public static function fromQuery(array $query): self
    {
        return new self(
            pair: $query['pair'] ?? StringHelper::EMPTY_STRING,
        );
    }
}
