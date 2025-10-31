<?php

namespace Kuaidi100QueryBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 测试KuaidiCompanyRepository的基本功能
 *
 * @internal
 */
#[CoversClass(KuaidiCompanyRepository::class)]
#[RunTestsInSeparateProcesses]
final class KuaidiCompanyRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function createNewEntity(): object
    {
        $company = new KuaidiCompany();
        $company->setName('test_company_' . uniqid());
        $company->setCode('test_code_' . uniqid());

        return $company;
    }

    /**
     * @return KuaidiCompanyRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(KuaidiCompanyRepository::class);
    }

    public function testRepositoryImplementation(): void
    {
        $repository = self::getService(KuaidiCompanyRepository::class);
        $this->assertInstanceOf(KuaidiCompanyRepository::class, $repository);
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }
}
