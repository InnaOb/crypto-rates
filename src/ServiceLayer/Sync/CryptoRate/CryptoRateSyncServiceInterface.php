<?php

declare(strict_types=1);

namespace CryptoRate\ServiceLayer\Sync\CryptoRate;

interface CryptoRateSyncServiceInterface
{
    public function sync(): void;
}
