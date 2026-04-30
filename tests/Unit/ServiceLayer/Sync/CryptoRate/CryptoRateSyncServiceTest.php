<?php

declare(strict_types=1);

namespace CryptoRate\Tests\Unit\ServiceLayer\Sync\CryptoRate;

use CryptoRate\DataLayer\Collection\CryptoRateCollection;
use CryptoRate\DataLayer\Entity\CryptoRate;
use CryptoRate\DataLayer\Repository\CryptoRateRepositoryInterface;
use CryptoRate\ServiceLayer\Provider\CryptoRate\CryptoRateProviderInterface;
use CryptoRate\ServiceLayer\Sync\CryptoRate\CryptoRateSyncService;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 */
final class CryptoRateSyncServiceTest extends TestCase
{
    private CryptoRateProviderInterface&MockObject $cryptoRateProviderMock;
    private CryptoRateRepositoryInterface&MockObject $cryptoRateRepositoryMock;

    private CryptoRateSyncService $cryptoRateSyncService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cryptoRateProviderMock = $this->createMock(CryptoRateProviderInterface::class);
        $this->cryptoRateRepositoryMock = $this->createMock(CryptoRateRepositoryInterface::class);

        $this->cryptoRateSyncService = new CryptoRateSyncService(
            $this->cryptoRateProviderMock,
            $this->cryptoRateRepositoryMock,
        );

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->cryptoRateProviderMock,
            $this->cryptoRateRepositoryMock,
            $this->cryptoRateSyncService,
        );
    }

    public function testSyncSavesRatesReturnedByProvider(): void
    {
        $collection = CryptoRateCollection::fromEntities([
            new CryptoRate('EUR/BTC', '62345.12000000', new DateTimeImmutable()),
            new CryptoRate('EUR/ETH', '3120.45000000', new DateTimeImmutable()),
        ]);

        $this->cryptoRateProviderMock
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn($collection);

        $this->cryptoRateRepositoryMock
            ->expects($this->once())
            ->method('saveAll')
            ->with($collection);

        $this->cryptoRateSyncService->sync();
    }

    public function testSyncDoesNotCallSaveWhenProviderReturnsEmptyCollection(): void
    {
        $this->cryptoRateProviderMock
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn(CryptoRateCollection::empty());

        $this->cryptoRateRepositoryMock
            ->expects($this->never())
            ->method('saveAll');

        $this->cryptoRateSyncService->sync();
    }

    public function testSyncRethrowsExceptionWhenSaveAllFails(): void
    {
        $this->cryptoRateProviderMock
            ->method('fetchAll')
            ->willReturn(CryptoRateCollection::fromEntities([
                new CryptoRate('EUR/BTC', '62345.12000000', new DateTimeImmutable()),
            ]));

        $exception = new RuntimeException('DB connection lost');

        $this->cryptoRateRepositoryMock
            ->method('saveAll')
            ->willThrowException($exception);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('DB connection lost');

        $this->cryptoRateSyncService->sync();
    }
}
