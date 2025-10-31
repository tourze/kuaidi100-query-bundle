<?php

namespace Kuaidi100QueryBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Kuaidi100QueryBundle\Repository\LogisticsStatusRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 测试LogisticsStatusRepository的基本功能
 *
 * @internal
 */
#[CoversClass(LogisticsStatusRepository::class)]
#[RunTestsInSeparateProcesses]
final class LogisticsStatusRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function createNewEntity(): object
    {
        // Create a LogisticsNum entity first since LogisticsStatus requires it
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('test_company_' . uniqid());
        $logisticsNum->setNumber('test_number_' . uniqid());

        $logisticsStatus = new LogisticsStatus();
        $logisticsStatus->setSn('test_sn_' . uniqid());
        $logisticsStatus->setCompanyCode('test_company_' . uniqid());
        $logisticsStatus->setContext('test_context_' . uniqid());
        $logisticsStatus->setFtime('2024-01-01 12:00:00');
        $logisticsStatus->setFlag('test_flag_' . uniqid());
        $logisticsStatus->setNumber($logisticsNum); // Set the required relationship

        return $logisticsStatus;
    }

    /**
     * @return LogisticsStatusRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(LogisticsStatusRepository::class);
    }

    public function testRepositoryImplementation(): void
    {
        $repository = self::getService(LogisticsStatusRepository::class);
        $this->assertInstanceOf(LogisticsStatusRepository::class, $repository);
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }
}
