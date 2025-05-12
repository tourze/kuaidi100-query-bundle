<?php

namespace Kuaidi100QueryBundle\Repository;

use Carbon\CarbonInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kuaidi100QueryBundle\Entity\LogisticsNum;

/**
 * @method LogisticsNum|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogisticsNum|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogisticsNum[]    findAll()
 * @method LogisticsNum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogisticsNumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogisticsNum::class);
    }

    /**
     * @return array|LogisticsNum[]
     */
    public function findNeedSyncList(CarbonInterface $now): array
    {
        // TODO 这里应该还有一个时间范围
        return $this->createQueryBuilder('a')
            ->where('a.syncTime IS NULL OR a.syncTime < :lastTIme')
            ->setParameter('lastTIme', $now->clone()->subMinutes(31))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return LogisticsNum[]
     */
    public function findUnsubscribedList(): array
    {
        // TODO 这里应该还有一个时间范围
        return $this->findBy(['subscribed' => null]);
    }
}
