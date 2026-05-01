<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Enum\Crypto;

enum CurrencyPairEnum: string
{
    case EUR_BTC = 'EUR/BTC';
    case EUR_ETH = 'EUR/ETH';
    case EUR_LTC = 'EUR/LTC';

    public function toBinanceSymbol(): string
    {
        return match($this) {
            self::EUR_BTC => 'BTCEUR',
            self::EUR_ETH => 'ETHEUR',
            self::EUR_LTC => 'LTCEUR',
        };
    }

    public function toStorageKey(): string
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn(self $case) => $case->value, self::cases());
    }

    public static function fromBinanceSymbol(string $symbol): self
    {
        return match($symbol) {
            'BTCEUR' => self::EUR_BTC,
            'ETHEUR' => self::EUR_ETH,
            'LTCEUR' => self::EUR_LTC,
            default  => throw new \ValueError(sprintf('Unknown Binance symbol: %s', $symbol)),
        };
    }
}
