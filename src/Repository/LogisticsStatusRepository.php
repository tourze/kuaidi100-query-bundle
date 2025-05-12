<?php

namespace Kuaidi100QueryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;

/**
 * @method LogisticsStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogisticsStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogisticsStatus[]    findAll()
 * @method LogisticsStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogisticsStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogisticsStatus::class);
    }
}
