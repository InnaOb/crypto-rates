<?php

declare(strict_types=1);

namespace CryptoRate\BusinessLogicLayer\Scheduler;

use DateTimeImmutable;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('fetch_crypto_rates')]
final readonly class FetchCryptoRatesScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every(
                300,
                new FetchCryptoRatesMessage(),
                new DateTimeImmutable('-299 seconds'),
            )
        );
    }
}
