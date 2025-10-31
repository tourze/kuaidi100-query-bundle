<?php

namespace Kuaidi100QueryBundle\Repository;

use Carbon\CarbonInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<LogisticsNum>
 */
#[AsRepository(entityClass: LogisticsNum::class)]
class LogisticsNumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogisticsNum::class);
    }

    /**
     * @return LogisticsNum[]
     */
    public function findNeedSyncList(CarbonInterface $now): array
    {
        // TODO 这里应该还有一个时间范围
        /** @var LogisticsNum[] $result */
        $result = $this->createQueryBuilder('a')
            ->where('a.syncTime IS NULL OR a.syncTime < :lastTime')
            ->setParameter('lastTime', $now->clone()->subMinutes(31))
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return LogisticsNum[]
     */
    public function findUnsubscribedList(): array
    {
        // TODO 这里应该还有一个时间范围
        return $this->findBy(['subscribed' => null]);
    }

    public function save(LogisticsNum $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LogisticsNum $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
