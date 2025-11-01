<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Tests\Controller\Admin;

use Kuaidi100QueryBundle\Controller\Admin\LogisticsStatusCrudController;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Repository\LogisticsStatusRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(LogisticsStatusCrudController::class)]
#[RunTestsInSeparateProcesses]
final class LogisticsStatusCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return LogisticsStatus::class;
    }

    public function testIndexPage(): void
    {
        $client = self::createAuthenticatedClient();
        $crawler = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Navigate to LogisticsStatus CRUD
        $link = $crawler->filter('a[href*="LogisticsStatusCrudController"]')->first();
        if ($link->count() > 0) {
            $client->click($link->link());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    public function testCreateLogisticsStatus(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Create a test logistics number first
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('shunfeng');
        $logisticsNum->setNumber('SF' . uniqid());

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);
        $logisticsNumRepository->save($logisticsNum, true);

        // Test form submission for new logistics status
        $logisticsStatus = new LogisticsStatus();
        $logisticsStatus->setSn($logisticsNum->getNumber() ?? 'test-sn-' . uniqid());
        $logisticsStatus->setCompanyCode('shunfeng');
        $logisticsStatus->setContext('快件已发出');
        $logisticsStatus->setFtime('2024-01-15 10:30:00');
        $logisticsStatus->setLocation('深圳分拨中心');
        $logisticsStatus->setAreaCenter('114.0579,22.5431');
        $logisticsStatus->setFlag('pickup-' . uniqid());
        $logisticsStatus->setState(LogisticsStateEnum::PICKUP);
        $logisticsStatus->setNumber($logisticsNum);

        $logisticsStatusRepository = self::getService(LogisticsStatusRepository::class);
        self::assertInstanceOf(LogisticsStatusRepository::class, $logisticsStatusRepository);
        $logisticsStatusRepository->save($logisticsStatus, true);

        // Verify logistics status was created
        $savedLogisticsStatus = self::getEntityManager()->getRepository(LogisticsStatus::class)->findOneBy(['flag' => $logisticsStatus->getFlag()]);
        $this->assertNotNull($savedLogisticsStatus);
        $this->assertEquals($logisticsStatus->getSn(), $savedLogisticsStatus->getSn());
        $this->assertEquals($logisticsStatus->getCompanyCode(), $savedLogisticsStatus->getCompanyCode());
        $this->assertEquals($logisticsStatus->getContext(), $savedLogisticsStatus->getContext());
        $this->assertEquals($logisticsStatus->getFtime(), $savedLogisticsStatus->getFtime());
        $this->assertEquals($logisticsStatus->getLocation(), $savedLogisticsStatus->getLocation());
        $this->assertEquals(LogisticsStateEnum::PICKUP, $savedLogisticsStatus->getState());
    }

    public function testLogisticsStatusDataPersistence(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Create test logistics number
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('yuantong');
        $logisticsNum->setNumber('YT' . uniqid());

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);
        $logisticsNumRepository->save($logisticsNum, true);

        // Create test logistics statuses with different configurations
        $logisticsStatus1 = new LogisticsStatus();
        $logisticsStatus1->setSn($logisticsNum->getNumber() ?? 'test-sn-' . uniqid());
        $logisticsStatus1->setCompanyCode('yuantong');
        $logisticsStatus1->setContext('快件已揽收');
        $logisticsStatus1->setFtime('2024-01-15 09:00:00');
        $logisticsStatus1->setLocation('上海转运中心');
        $logisticsStatus1->setAreaCenter('121.4737,31.2304');
        $logisticsStatus1->setFlag('pickup-' . uniqid());
        $logisticsStatus1->setState(LogisticsStateEnum::PICKUP);
        $logisticsStatus1->setNumber($logisticsNum);

        $logisticsStatusRepository = self::getService(LogisticsStatusRepository::class);
        self::assertInstanceOf(LogisticsStatusRepository::class, $logisticsStatusRepository);
        $logisticsStatusRepository->save($logisticsStatus1, true);

        $logisticsStatus2 = new LogisticsStatus();
        $logisticsStatus2->setSn($logisticsNum->getNumber() ?? 'test-sn-' . uniqid());
        $logisticsStatus2->setCompanyCode('yuantong');
        $logisticsStatus2->setContext('快件正在运输途中');
        $logisticsStatus2->setFtime('2024-01-15 14:30:00');
        $logisticsStatus2->setLocation('南京中转站');
        $logisticsStatus2->setFlag('onway-' . uniqid());
        $logisticsStatus2->setState(LogisticsStateEnum::ONWAY);
        $logisticsStatus2->setNumber($logisticsNum);
        $logisticsStatusRepository->save($logisticsStatus2, true);

        // Verify logistics statuses are saved correctly
        $savedLogisticsStatus1 = $logisticsStatusRepository->findOneBy(['flag' => $logisticsStatus1->getFlag()]);
        $this->assertNotNull($savedLogisticsStatus1);
        $this->assertEquals('快件已揽收', $savedLogisticsStatus1->getContext());
        $this->assertEquals(LogisticsStateEnum::PICKUP, $savedLogisticsStatus1->getState());
        $this->assertEquals('上海转运中心', $savedLogisticsStatus1->getLocation());

        $savedLogisticsStatus2 = $logisticsStatusRepository->findOneBy(['flag' => $logisticsStatus2->getFlag()]);
        $this->assertNotNull($savedLogisticsStatus2);
        $this->assertEquals('快件正在运输途中', $savedLogisticsStatus2->getContext());
        $this->assertEquals(LogisticsStateEnum::ONWAY, $savedLogisticsStatus2->getState());
        $this->assertEquals('南京中转站', $savedLogisticsStatus2->getLocation());
    }

    public function testLogisticsStatusStringRepresentation(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Create test logistics number
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('zhongtong');
        $logisticsNum->setNumber('ZT123456789');

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);
        $logisticsNumRepository->save($logisticsNum, true);

        $logisticsStatus = new LogisticsStatus();
        $logisticsStatus->setSn('ZT123456789');
        $logisticsStatus->setCompanyCode('zhongtong');
        $logisticsStatus->setContext('快件已签收，签收人：本人');
        $logisticsStatus->setFtime('2024-01-16 15:45:00');
        $logisticsStatus->setFlag('sign-' . uniqid());
        $logisticsStatus->setState(LogisticsStateEnum::SIGN);
        $logisticsStatus->setNumber($logisticsNum);

        $logisticsStatusRepository = self::getService(LogisticsStatusRepository::class);
        self::assertInstanceOf(LogisticsStatusRepository::class, $logisticsStatusRepository);
        $logisticsStatusRepository->save($logisticsStatus, true);

        // Test __toString method
        $this->assertEquals('快件已签收，签收人：本人', $logisticsStatus->__toString());
    }

    public function testLogisticsStatusWithAllStates(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Create test logistics number
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('ems');
        $logisticsNum->setNumber('EMS' . uniqid());

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);
        $logisticsNumRepository->save($logisticsNum, true);

        $logisticsStatusRepository = self::getService(LogisticsStatusRepository::class);
        self::assertInstanceOf(LogisticsStatusRepository::class, $logisticsStatusRepository);

        // Test all possible logistics states
        $statesData = [
            [LogisticsStateEnum::PICKUP, '快件已揽收', 'pickup'],
            [LogisticsStateEnum::ONWAY, '快件运输中', 'onway'],
            [LogisticsStateEnum::DELIVER, '快件正在派送', 'deliver'],
            [LogisticsStateEnum::SIGN, '快件已签收', 'sign'],
            [LogisticsStateEnum::RETURN, '快件退回中', 'return'],
        ];

        foreach ($statesData as $index => [$state, $context, $flagPrefix]) {
            $logisticsStatus = new LogisticsStatus();
            $logisticsStatus->setSn($logisticsNum->getNumber() ?? 'test-sn-' . uniqid());
            $logisticsStatus->setCompanyCode('ems');
            $logisticsStatus->setContext($context);
            $logisticsStatus->setFtime('2024-01-' . (15 + $index) . ' 10:00:00');
            $logisticsStatus->setFlag($flagPrefix . '-' . uniqid());
            $logisticsStatus->setState($state);
            $logisticsStatus->setNumber($logisticsNum);

            $logisticsStatusRepository->save($logisticsStatus, true);

            $savedLogisticsStatus = $logisticsStatusRepository->findOneBy(['flag' => $logisticsStatus->getFlag()]);
            $this->assertNotNull($savedLogisticsStatus);
            $this->assertEquals($state, $savedLogisticsStatus->getState());
            $this->assertEquals($context, $savedLogisticsStatus->getContext());

            // Test state label
            $this->assertNotEmpty($state->getLabel());
        }
    }

    public function testLogisticsStatusOptionalFields(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Create test logistics number
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('shentong');
        $logisticsNum->setNumber('ST' . uniqid());

        $logisticsNumRepository = self::getService(LogisticsNumRepository::class);
        self::assertInstanceOf(LogisticsNumRepository::class, $logisticsNumRepository);
        $logisticsNumRepository->save($logisticsNum, true);

        // Test logistics status with only required fields
        $logisticsStatus = new LogisticsStatus();
        $logisticsStatus->setSn($logisticsNum->getNumber() ?? 'test-sn-' . uniqid());
        $logisticsStatus->setCompanyCode('shentong');
        $logisticsStatus->setContext('包裹已发出');
        $logisticsStatus->setFtime('2024-01-15 08:00:00');
        $logisticsStatus->setFlag('minimal-' . uniqid());
        $logisticsStatus->setNumber($logisticsNum);

        $logisticsStatusRepository = self::getService(LogisticsStatusRepository::class);
        self::assertInstanceOf(LogisticsStatusRepository::class, $logisticsStatusRepository);
        $logisticsStatusRepository->save($logisticsStatus, true);

        $savedLogisticsStatus = $logisticsStatusRepository->findOneBy(['flag' => $logisticsStatus->getFlag()]);
        $this->assertNotNull($savedLogisticsStatus);
        $this->assertEquals('包裹已发出', $savedLogisticsStatus->getContext());
        $this->assertNull($savedLogisticsStatus->getLocation());
        $this->assertNull($savedLogisticsStatus->getAreaCenter());
        $this->assertNull($savedLogisticsStatus->getState());
    }

    /**
     * @return LogisticsStatusCrudController
     */
    protected function getControllerService(): LogisticsStatusCrudController
    {
        return self::getService(LogisticsStatusCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'sn' => ['快递单号'];
        yield 'companyCode' => ['物流公司编码'];
        yield 'context' => ['内容'];
        yield 'ftime' => ['到达时间'];
        yield 'location' => ['当前位置'];
        yield 'flag' => ['状态标识'];
        yield 'number' => ['关联物流单号'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'sn' => ['sn'];
        yield 'companyCode' => ['companyCode'];
        yield 'context' => ['context'];
        yield 'ftime' => ['ftime'];
        yield 'location' => ['location'];
        yield 'areaCenter' => ['areaCenter'];
        yield 'flag' => ['flag'];
        yield 'state' => ['state'];
        yield 'number' => ['number'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'sn' => ['sn'];
        yield 'companyCode' => ['companyCode'];
        yield 'context' => ['context'];
        yield 'ftime' => ['ftime'];
        yield 'location' => ['location'];
        yield 'areaCenter' => ['areaCenter'];
        yield 'flag' => ['flag'];
        yield 'state' => ['state'];
        yield 'number' => ['number'];
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
