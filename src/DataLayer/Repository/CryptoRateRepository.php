<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Repository;

use CryptoRate\DataLayer\Collection\CryptoRateCollection;
use CryptoRate\DataLayer\Entity\CryptoRate;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CryptoRateRepository extends ServiceEntityRepository implements CryptoRateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryptoRate::class);
    }

    public function save(CryptoRate $cryptoRate): void
    {
        $this->getEntityManager()->persist($cryptoRate);
        $this->getEntityManager()->flush();
    }

    public function saveAll(CryptoRateCollection $rates): void
    {
        foreach ($rates as $rate) {
            $this->getEntityManager()->persist($rate);
        }
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function findLast24h(string $pair): CryptoRateCollection
    {
        return CryptoRateCollection::fromEntities(
            $this->createQueryBuilder('cr')
                ->where('cr.pair = :pair')
                ->andWhere('cr.recordedAt >= :from')
                ->setParameter('pair', $pair)
                ->setParameter('from', new DateTimeImmutable('-24 hours'))
                ->orderBy('cr.recordedAt', 'ASC')
                ->getQuery()
                ->getResult()
        );
    }

    public function findByDay(string $pair, DateTimeImmutable $date): CryptoRateCollection
    {
        return CryptoRateCollection::fromEntities(
            $this->createQueryBuilder('cr')
                ->where('cr.pair = :pair')
                ->andWhere('cr.recordedAt >= :from AND cr.recordedAt < :to')
                ->setParameter('pair', $pair)
                ->setParameter('from', $date->setTime(0, 0, 0))
                ->setParameter('to', $date->modify('+1 day')->setTime(0, 0, 0))
                ->orderBy('cr.recordedAt', 'ASC')
                ->getQuery()
                ->getResult()
        );
    }
}
