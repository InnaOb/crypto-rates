<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\DTO\Request;

interface RequestDtoInterface
{
    public static function fromQuery(array $query): mixed;
}
