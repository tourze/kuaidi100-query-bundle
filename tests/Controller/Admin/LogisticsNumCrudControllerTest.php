<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Tests\Controller\Admin;

use Kuaidi100QueryBundle\Controller\Admin\LogisticsNumCrudController;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(LogisticsNumCrudController::class)]
#[RunTestsInSeparateProcesses]
final class LogisticsNumCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return LogisticsNum::class;
    }

    /**
     * @return LogisticsNumCrudController
     */
    protected function getControllerService(): LogisticsNumCrudController
    {
        return self::getService(LogisticsNumCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'company' => ['快递公司编码'];
        yield 'number' => ['快递单号'];
        yield 'phoneNumber' => ['电话号码'];
        yield 'fromCity' => ['出发地城市'];
        yield 'toCity' => ['目的地城市'];
        yield 'account' => ['关联账号'];
        yield 'subscribed' => ['是否订阅推送'];
        yield 'syncTime' => ['上次同步时间'];
        yield 'latestStatus' => ['物流状态'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'company' => ['company'];
        yield 'number' => ['number'];
        yield 'phoneNumber' => ['phoneNumber'];
        yield 'fromCity' => ['fromCity'];
        yield 'toCity' => ['toCity'];
        yield 'subscribed' => ['subscribed'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'company' => ['company'];
        yield 'number' => ['number'];
        yield 'phoneNumber' => ['phoneNumber'];
        yield 'fromCity' => ['fromCity'];
        yield 'toCity' => ['toCity'];
        yield 'subscribed' => ['subscribed'];
    }

    public function testIndexPage(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);
        $crawler = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Navigate to LogisticsNum CRUD
        $link = $crawler->filter('a[href*="LogisticsNumCrudController"]')->first();
        if ($link->count() > 0) {
            $client->click($link->link());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    public function testCreateLogisticsNum(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);
        $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Create a test account first
        $account = new Account();
        $account->setValid(true);
        $account->setCustomer('test-customer-' . uniqid());
        $account->setUserid('test-userid-' . uniqid());
        $account->setSecret('test-secret-' . uniqid());
        $account->setSignKey('test-signkey-' . uniqid());

        $accountRepository = self::getService(AccountRepository::class);
        self::assertInstanceOf(AccountRepository::class, $accountRepository);
        $accountRepository->save($account, true);

        // Test form submission for new logistics number
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('shunfeng');
        $logisticsNum->setNumber('SF' . uniqid());
        $logisticsNum->setPhoneNumber('13800138000');
        $logisticsNum->setFromCity('深圳市');
        $logisticsNum->setToCity('北京市');
        $logisticsNum->setLatestStatus('已发货');
        $logisticsNum->setSubscribed(true);
        $logisticsNum->setAccount($account);
        $logisticsNum->setSyncTime(new \DateTimeImmutable());

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);
        $logisticsNumRepository->save($logisticsNum, true);

        // Verify logistics number was created
        $savedLogisticsNum = $logisticsNumRepository->findOneBy(['number' => $logisticsNum->getNumber()]);
        $this->assertNotNull($savedLogisticsNum);
        $this->assertEquals($logisticsNum->getNumber(), $savedLogisticsNum->getNumber());
        $this->assertEquals($logisticsNum->getCompany(), $savedLogisticsNum->getCompany());
        $this->assertEquals($logisticsNum->getPhoneNumber(), $savedLogisticsNum->getPhoneNumber());
        $this->assertEquals($logisticsNum->getFromCity(), $savedLogisticsNum->getFromCity());
        $this->assertEquals($logisticsNum->getToCity(), $savedLogisticsNum->getToCity());
        $this->assertTrue($savedLogisticsNum->isSubscribed());
    }

    public function testLogisticsNumDataPersistence(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Create test account
        $account = new Account();
        $account->setValid(true);
        $account->setCustomer('persistence-test-' . uniqid());
        $account->setUserid('user-' . uniqid());
        $account->setSecret('secret-' . uniqid());
        $account->setSignKey('signkey-' . uniqid());

        $accountRepository = self::getService(AccountRepository::class);
        self::assertInstanceOf(AccountRepository::class, $accountRepository);
        $accountRepository->save($account, true);

        // Create test logistics numbers with different configurations
        $logisticsNum1 = new LogisticsNum();
        $logisticsNum1->setCompany('yuantong');
        $logisticsNum1->setNumber('YT' . uniqid());
        $logisticsNum1->setPhoneNumber('13900139000');
        $logisticsNum1->setFromCity('上海市');
        $logisticsNum1->setToCity('广州市');
        $logisticsNum1->setLatestStatus('运输中');
        $logisticsNum1->setSubscribed(true);
        $logisticsNum1->setAccount($account);

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);
        $logisticsNumRepository->save($logisticsNum1, true);

        $logisticsNum2 = new LogisticsNum();
        $logisticsNum2->setCompany('zhongtong');
        $logisticsNum2->setNumber('ZT' . uniqid());
        $logisticsNum2->setFromCity('杭州市');
        $logisticsNum2->setToCity('成都市');
        $logisticsNum2->setSubscribed(false);
        $logisticsNumRepository->save($logisticsNum2, true);

        // Verify logistics numbers are saved correctly
        $savedLogisticsNum1 = $logisticsNumRepository->findOneBy(['number' => $logisticsNum1->getNumber()]);
        $this->assertNotNull($savedLogisticsNum1);
        $this->assertEquals('yuantong', $savedLogisticsNum1->getCompany());
        $this->assertEquals('运输中', $savedLogisticsNum1->getLatestStatus());
        $this->assertTrue($savedLogisticsNum1->isSubscribed());
        $this->assertEquals($account->getId(), $savedLogisticsNum1->getAccount()?->getId());

        $savedLogisticsNum2 = $logisticsNumRepository->findOneBy(['number' => $logisticsNum2->getNumber()]);
        $this->assertNotNull($savedLogisticsNum2);
        $this->assertEquals('zhongtong', $savedLogisticsNum2->getCompany());
        $this->assertFalse($savedLogisticsNum2->isSubscribed());
        $this->assertNull($savedLogisticsNum2->getAccount());
    }

    public function testLogisticsNumStringRepresentation(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('shentong');
        $logisticsNum->setNumber('ST123456789');
        $logisticsNum->setFromCity('武汉市');
        $logisticsNum->setToCity('西安市');

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);
        $logisticsNumRepository->save($logisticsNum, true);

        // Test __toString method
        $this->assertEquals('ST123456789', $logisticsNum->__toString());
    }

    public function testLogisticsNumWithValidPhoneNumber(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Test various valid phone number formats
        $validPhoneNumbers = [
            '13800138000',
            '+86 138-0013-8000',
            '(010) 12345678',
            '138 0013 8000',
            '+86-138-0013-8000',
        ];

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);

        foreach ($validPhoneNumbers as $index => $phoneNumber) {
            $logisticsNum = new LogisticsNum();
            $logisticsNum->setCompany('yunda');
            $logisticsNum->setNumber('YD' . $index . uniqid());
            $logisticsNum->setPhoneNumber($phoneNumber);

            $logisticsNumRepository->save($logisticsNum, true);

            $savedLogisticsNum = $logisticsNumRepository->findOneBy(['number' => $logisticsNum->getNumber()]);
            $this->assertNotNull($savedLogisticsNum);
            $this->assertEquals($phoneNumber, $savedLogisticsNum->getPhoneNumber());
        }
    }

    public function testLogisticsNumOptionalFields(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Test logistics number with only required fields
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('ems');
        $logisticsNum->setNumber('EMS' . uniqid());

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);
        $logisticsNumRepository->save($logisticsNum, true);

        $savedLogisticsNum = $logisticsNumRepository->findOneBy(['number' => $logisticsNum->getNumber()]);
        $this->assertNotNull($savedLogisticsNum);
        $this->assertEquals('ems', $savedLogisticsNum->getCompany());
        $this->assertNull($savedLogisticsNum->getPhoneNumber());
        $this->assertNull($savedLogisticsNum->getFromCity());
        $this->assertNull($savedLogisticsNum->getToCity());
        $this->assertNull($savedLogisticsNum->getLatestStatus());
        $this->assertNull($savedLogisticsNum->getSyncTime());
        $this->assertNull($savedLogisticsNum->isSubscribed());
        $this->assertNull($savedLogisticsNum->getAccount());
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
