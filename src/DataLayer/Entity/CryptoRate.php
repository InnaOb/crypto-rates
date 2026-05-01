<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Entity;

use CryptoRate\DataLayer\Repository\CryptoRateRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryptoRateRepository::class)]
#[ORM\Table(name: 'crypto_rates')]
#[ORM\Index(name: 'idx_pair_recorded_at', columns: ['pair', 'recorded_at'])]
class CryptoRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 10)]
    private string $pair;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private string $rate;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $recordedAt;

    public function __construct(string $pair, string $rate, DateTimeImmutable $recordedAt)
    {
        $this->pair = $pair;
        $this->rate = $rate;
        $this->recordedAt = $recordedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPair(): string
    {
        return $this->pair;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function getRecordedAt(): DateTimeImmutable
    {
        return $this->recordedAt;
    }
}
