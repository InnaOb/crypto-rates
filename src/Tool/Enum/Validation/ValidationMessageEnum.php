<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Enum\Validation;

enum ValidationMessageEnum: string
{
    case NOT_BLANK = 'This value should not be blank';
    case INVALID_PAIR = 'Invalid currency pair. Allowed: EUR/BTC, EUR/ETH, EUR/LTC';
    case INVALID_DATE = 'Invalid date format. Expected: YYYY-MM-DD';
}
