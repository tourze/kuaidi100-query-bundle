<?php

namespace Kuaidi100QueryBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Controller\QueryAction;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use Kuaidi100QueryBundle\Service\LogisticsService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * 测试QueryAction的基本功能
 */
class QueryActionTest extends TestCase
{
    private QueryAction $action;
    private LogisticsNumRepository|MockObject $logisticsNumRepository;
    private EntityManagerInterface|MockObject $entityManager;
    private KuaidiCompanyRepository|MockObject $companyRepository;
    private Kuaidi100Service|MockObject $service;
    private LogisticsService|MockObject $logisticsService;
    private AccountRepository|MockObject $accountRepository;
    
    public function testInvokeWithExistingLogisticsNum(): void
    {
        $account = $this->createMock(Account::class);
        $company = $this->createMock(KuaidiCompany::class);
        $logisticsNum = $this->createMock(LogisticsNum::class);

        $company->expects($this->once())
            ->method('getCode')
            ->willReturn('yuantong');

        $request = new Request([
            'company' => '圆通速递',
            'sn' => '1234567890',
            'phone' => '13800138000'
        ]);

        $this->companyRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => '圆通速递'])
            ->willReturn($company);

        $this->accountRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['valid' => true])
            ->willReturn($account);

        $this->service
            ->expects($this->once())
            ->method('request')
            ->willReturn(['result' => 'success']);

        $this->logisticsNumRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['number' => '1234567890'])
            ->willReturn($logisticsNum);

        $this->logisticsService
            ->expects($this->once())
            ->method('syncStatusToDb');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = ($this->action)($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
    public function testInvokeWithoutAccount(): void
    {
        $company = $this->createMock(KuaidiCompany::class);
        $request = new Request([
            'company' => '圆通速递',
            'sn' => '1234567890'
        ]);

        $this->companyRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => '圆通速递'])
            ->willReturn($company);

        $this->accountRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['valid' => true])
            ->willReturn(null);

        $this->expectException(AccountNotFoundException::class);

        ($this->action)($request);
    }
    
    protected function setUp(): void
    {
        $this->logisticsNumRepository = $this->createMock(LogisticsNumRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->companyRepository = $this->createMock(KuaidiCompanyRepository::class);
        $this->service = $this->createMock(Kuaidi100Service::class);
        $this->logisticsService = $this->createMock(LogisticsService::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);

        $this->action = new QueryAction(
            $this->logisticsNumRepository,
            $this->entityManager,
            $this->companyRepository,
            $this->service,
            $this->logisticsService,
            $this->accountRepository
        );

        // 设置容器
        $container = new Container();
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->any())
            ->method('serialize')
            ->willReturn(json_encode(['result' => 'success']));
        $container->set('serializer', $serializer);
        $this->action->setContainer($container);
    }
} 