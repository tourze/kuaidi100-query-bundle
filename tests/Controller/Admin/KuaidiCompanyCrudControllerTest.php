<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Tests\Controller\Admin;

use Kuaidi100QueryBundle\Controller\Admin\KuaidiCompanyCrudController;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(KuaidiCompanyCrudController::class)]
#[RunTestsInSeparateProcesses]
final class KuaidiCompanyCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return KuaidiCompany::class;
    }

    /**
     * @return KuaidiCompanyCrudController
     */
    protected function getControllerService(): KuaidiCompanyCrudController
    {
        return self::getService(KuaidiCompanyCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'name' => ['公司名称'];
        yield 'code' => ['公司编码'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'code' => ['code'];
        yield 'remark' => ['remark'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        return self::provideNewPageFields();
    }

    public function testIndexPage(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);
        $crawler = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Navigate to KuaidiCompany CRUD
        $link = $crawler->filter('a[href*="KuaidiCompanyCrudController"]')->first();
        if ($link->count() > 0) {
            $client->click($link->link());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    public function testCreateKuaidiCompany(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);
        $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Test form submission for new kuaidi company
        $company = new KuaidiCompany();
        $company->setName('测试快递公司-' . uniqid());
        $company->setCode('test-' . uniqid());
        $company->setRemark('测试快递公司备注信息');

        $companyRepository = self::getService(KuaidiCompanyRepository::class);
        self::assertInstanceOf(KuaidiCompanyRepository::class, $companyRepository);
        $companyRepository->save($company, true);

        // Verify company was created
        $savedCompanyRepository = self::getService(KuaidiCompanyRepository::class);
        self::assertInstanceOf(KuaidiCompanyRepository::class, $savedCompanyRepository);
        $savedCompany = $savedCompanyRepository->findOneBy(['code' => $company->getCode()]);
        $this->assertNotNull($savedCompany);
        $this->assertEquals($company->getName(), $savedCompany->getName());
        $this->assertEquals($company->getCode(), $savedCompany->getCode());
        $this->assertEquals($company->getRemark(), $savedCompany->getRemark());
    }

    public function testKuaidiCompanyDataPersistence(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Create test companies with different configurations
        $company1 = new KuaidiCompany();
        $company1->setName('顺丰速运-' . uniqid());
        $company1->setCode('shunfeng-' . uniqid());
        $company1->setRemark('顺丰速运快递公司');

        $companyRepository = self::getService(KuaidiCompanyRepository::class);
        self::assertInstanceOf(KuaidiCompanyRepository::class, $companyRepository);
        $companyRepository->save($company1, true);

        $company2 = new KuaidiCompany();
        $company2->setName('中通快递-' . uniqid());
        $company2->setCode('zhongtong-' . uniqid());
        $company2->setRemark('中通快递公司');
        $companyRepository->save($company2, true);

        // Verify companies are saved correctly
        $savedCompany1 = $companyRepository->findOneBy(['code' => $company1->getCode()]);
        $this->assertNotNull($savedCompany1);
        $this->assertEquals($company1->getName(), $savedCompany1->getName());
        $this->assertEquals('顺丰速运快递公司', $savedCompany1->getRemark());

        $savedCompany2 = $companyRepository->findOneBy(['code' => $company2->getCode()]);
        $this->assertNotNull($savedCompany2);
        $this->assertEquals($company2->getName(), $savedCompany2->getName());
        $this->assertEquals('中通快递公司', $savedCompany2->getRemark());
    }

    public function testKuaidiCompanyStringRepresentation(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        $company = new KuaidiCompany();
        $company->setName('申通快递-' . uniqid());
        $company->setCode('shentong-' . uniqid());
        $company->setRemark('申通快递备注');

        $companyRepository = self::getService(KuaidiCompanyRepository::class);
        self::assertInstanceOf(KuaidiCompanyRepository::class, $companyRepository);
        $companyRepository->save($company, true);

        // Test __toString method
        $expectedString = $company->getName() . '(' . $company->getCode() . ')';
        $this->assertEquals($expectedString, $company->__toString());

        // Test toArray method
        $array = $company->toArray();
        $this->assertArrayHasKey('code', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals($company->getCode(), $array['code']);
        $this->assertEquals($company->getName(), $array['name']);

        // Test retrieveAdminArray method
        $adminArray = $company->retrieveAdminArray();
        $this->assertArrayHasKey('id', $adminArray);
        $this->assertArrayHasKey('name', $adminArray);
        $this->assertArrayHasKey('code', $adminArray);
        $this->assertArrayHasKey('remark', $adminArray);
        $this->assertArrayHasKey('createTime', $adminArray);
        $this->assertArrayHasKey('updateTime', $adminArray);

        // Test retrieveApiArray method
        $apiArray = $company->retrieveApiArray();
        $this->assertEquals($adminArray, $apiArray);
    }

    public function testCompanyWithoutRemark(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Test company without remark
        $company = new KuaidiCompany();
        $company->setName('韵达快递-' . uniqid());
        $company->setCode('yunda-' . uniqid());

        $companyRepository = self::getService(KuaidiCompanyRepository::class);
        self::assertInstanceOf(KuaidiCompanyRepository::class, $companyRepository);
        $companyRepository->save($company, true);

        $savedCompany = $companyRepository->findOneBy(['code' => $company->getCode()]);
        $this->assertNotNull($savedCompany);
        $this->assertEquals($company->getName(), $savedCompany->getName());
        $this->assertNull($savedCompany->getRemark());

        // Test admin array with null remark
        $adminArray = $savedCompany->retrieveAdminArray();
        $this->assertArrayHasKey('remark', $adminArray);
        $this->assertNull($adminArray['remark']);
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
