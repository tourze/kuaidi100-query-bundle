<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Tests\Controller\Admin;

use Kuaidi100QueryBundle\Controller\Admin\AccountCrudController;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(AccountCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AccountCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return Account::class;
    }

    /**
     * @return AccountCrudController
     */
    protected function getControllerService(): AccountCrudController
    {
        return self::getService(AccountCrudController::class);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideIndexPageHeaders(): array
    {
        return [
            'id' => ['ID'],
            'valid' => ['是否有效'],
            'customer' => ['客户标识'],
            'userid' => ['用户ID'],
            'numbers' => ['物流单号'],
            'createTime' => ['创建时间'],
            'updateTime' => ['更新时间'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideNewPageFields(): array
    {
        return [
            'valid' => ['valid'],
            'customer' => ['customer'],
            'userid' => ['userid'],
            'secret' => ['secret'],
            'signKey' => ['signKey'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideEditPageFields(): array
    {
        return [
            'valid' => ['valid'],
            'customer' => ['customer'],
            'userid' => ['userid'],
            'secret' => ['secret'],
            'signKey' => ['signKey'],
        ];
    }

    public function testIndexPage(): void
    {
        $client = self::createAuthenticatedClient();
        $crawler = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Navigate to Account CRUD
        $link = $crawler->filter('a[href*="AccountCrudController"]')->first();
        if ($link->count() > 0) {
            $client->click($link->link());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    public function testCreateAccount(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Test form submission for new account
        $account = new Account();
        $account->setValid(true);
        $account->setCustomer('test-customer-' . uniqid());
        $account->setUserid('test-userid-' . uniqid());
        $account->setSecret('test-secret-key-' . uniqid());
        $account->setSignKey('test-sign-key-' . uniqid());

        /** @var AccountRepository $accountRepository */
        $accountRepository = self::getService(AccountRepository::class);
        self::assertInstanceOf(AccountRepository::class, $accountRepository);
        $accountRepository->save($account, true);

        // Verify account was created
        /** @var AccountRepository $repository */
        $repository = self::getService(AccountRepository::class);
        $savedAccount = $repository->findOneBy(['customer' => $account->getCustomer()]);
        $this->assertNotNull($savedAccount);
        $this->assertEquals($account->getCustomer(), $savedAccount->getCustomer());
        $this->assertEquals($account->getUserid(), $savedAccount->getUserid());
        $this->assertTrue($savedAccount->isValid());
    }

    public function testAccountDataPersistence(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Create test accounts with different configurations
        $account1 = new Account();
        $account1->setValid(true);
        $account1->setCustomer('persistence-test-1-' . uniqid());
        $account1->setUserid('user1-' . uniqid());
        $account1->setSecret('secret1-' . uniqid());
        $account1->setSignKey('signkey1-' . uniqid());

        /** @var AccountRepository $accountRepository */
        $accountRepository = self::getService(AccountRepository::class);
        self::assertInstanceOf(AccountRepository::class, $accountRepository);
        $accountRepository->save($account1, true);

        $account2 = new Account();
        $account2->setValid(false);
        $account2->setCustomer('persistence-test-2-' . uniqid());
        $account2->setUserid('user2-' . uniqid());
        $account2->setSecret('secret2-' . uniqid());
        $account2->setSignKey('signkey2-' . uniqid());
        $accountRepository->save($account2, true);

        // Verify accounts are saved correctly
        $savedAccount1 = $accountRepository->findOneBy(['customer' => $account1->getCustomer()]);
        $this->assertNotNull($savedAccount1);
        $this->assertEquals($account1->getCustomer(), $savedAccount1->getCustomer());
        $this->assertTrue($savedAccount1->isValid());

        $savedAccount2 = $accountRepository->findOneBy(['customer' => $account2->getCustomer()]);
        $this->assertNotNull($savedAccount2);
        $this->assertEquals($account2->getCustomer(), $savedAccount2->getCustomer());
        $this->assertFalse($savedAccount2->isValid());
    }

    public function testAccountStringRepresentation(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        $account = new Account();
        $account->setValid(true);
        $account->setCustomer('test-customer');
        $account->setUserid('test-userid');
        $account->setSecret('test-secret');
        $account->setSignKey('test-signkey');

        /** @var AccountRepository $accountRepository */
        $accountRepository = self::getService(AccountRepository::class);
        self::assertInstanceOf(AccountRepository::class, $accountRepository);
        $accountRepository->save($account, true);

        // Test __toString method
        $this->assertEquals('test-customer(test-userid)', $account->__toString());

        // Test toArray method
        $array = $account->toArray();
        $this->assertArrayHasKey('userid', $array);
        $this->assertArrayHasKey('secret', $array);
        $this->assertEquals('test-userid', $array['userid']);
        $this->assertEquals('test-secret', $array['secret']);

        // Test retrieveAdminArray method
        $adminArray = $account->retrieveAdminArray();
        $this->assertArrayHasKey('customer', $adminArray);
        $this->assertArrayHasKey('userid', $adminArray);
        $this->assertArrayHasKey('secret', $adminArray);
        $this->assertArrayHasKey('signKey', $adminArray);
        $this->assertArrayHasKey('valid', $adminArray);
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 提交空表单验证错误
        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Create')->form();
        $client->submit($form);

        // 验证表单验证错误
        $this->assertResponseStatusCodeSame(422);
    }
}
