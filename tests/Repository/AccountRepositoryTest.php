<?php

namespace Kuaidi100QueryBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 测试AccountRepository的基本功能
 *
 * @internal
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setCustomer('test_customer_' . uniqid());
        $account->setUserid('test_userid_' . uniqid());
        $account->setSecret('test_secret_' . uniqid());
        $account->setSignKey('test_sign_key_' . uniqid());
        $account->setValid(true);

        return $account;
    }

    /**
     * @return AccountRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(AccountRepository::class);
    }

    public function testRepositoryImplementation(): void
    {
        $repository = self::getService(AccountRepository::class);
        $this->assertInstanceOf(AccountRepository::class, $repository);
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }
}
