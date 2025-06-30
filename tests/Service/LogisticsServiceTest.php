<?php

namespace Kuaidi100QueryBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Kuaidi100QueryBundle\Repository\LogisticsStatusRepository;
use Kuaidi100QueryBundle\Request\Kuaidi100QueryRequest;
use Kuaidi100QueryBundle\Request\PollRequest;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use Kuaidi100QueryBundle\Service\LogisticsService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LogisticsServiceTest extends TestCase
{
    private LogisticsService $service;
    private $logisticsStatusRepository;
    private $companyRepository;
    private $apiService;
    private $urlGenerator;
    private $entityManager;
    
    protected function setUp(): void
    {
        $this->logisticsStatusRepository = $this->createMock(LogisticsStatusRepository::class);
        $this->companyRepository = $this->createMock(KuaidiCompanyRepository::class);
        $this->apiService = $this->createMock(Kuaidi100Service::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->service = new LogisticsService(
            $this->logisticsStatusRepository,
            $this->companyRepository,
            $this->apiService,
            $this->urlGenerator,
            $this->entityManager
        );
    }
    
    public function testQueryAndSyncWithValidLogisticsNum(): void
    {
        // 创建测试所需数据
        $account = new Account();
        $account->setCustomer('test_customer');
        $account->setSignKey('test_key');
        
        $number = new LogisticsNum();
        $number->setCompany('test_company');
        $number->setNumber('123456789');
        $number->setPhone('13800138000');
        $number->setAccount($account);
        
        $company = new KuaidiCompany();
        $company->setName('test_company');
        $company->setCode('test_code');
        
        // 设置模拟行为
        $this->companyRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'test_company'])
            ->willReturn($company);
        
        // 验证API请求构建
        $this->apiService->expects($this->once())
            ->method('request')
            ->willReturnCallback(function ($request) {
                $this->assertInstanceOf(Kuaidi100QueryRequest::class, $request);
                $this->assertEquals('test_code', $request->getCom());
                $this->assertEquals('123456789', $request->getNum());
                $this->assertEquals('13800138000', $request->getPhone());
                
                return [
                    'nu' => '123456789',
                    'com' => 'test_code',
                    'state' => 3,
                    'data' => [
                        ['context' => 'Test context 1', 'ftime' => '2023-01-01 10:00:00'],
                        ['context' => 'Test context 2', 'ftime' => '2023-01-01 11:00:00'],
                    ],
                ];
            });
        
        // 期望调用实体管理器来保存数据
        $this->entityManager->expects($this->atLeastOnce())
            ->method('persist');
        $this->entityManager->expects($this->atLeastOnce())
            ->method('flush');
        
        // 执行测试方法
        $this->service->queryAndSync($number);
        
        // 验证同步时间已设置
        $this->assertNotNull($number->getSyncTime());
    }
    
    public function testQueryAndSyncWithInvalidCompany(): void
    {
        // 创建测试所需数据
        $number = new LogisticsNum();
        $number->setCompany('invalid_company');
        
        // 设置模拟行为 - 找不到物流公司
        $this->companyRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'invalid_company'])
            ->willReturn(null);
        
        // 期望抛出异常
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('找不到指定的物流公司');
        
        // 执行测试方法
        $this->service->queryAndSync($number);
    }
    
    public function testSyncStatusToDb(): void
    {
        // 创建测试所需数据
        $number = new LogisticsNum();
        $number->setNumber('123456789');
        
        $apiResponse = [
            'nu' => '123456789',
            'com' => 'test_code',
            'state' => 3,
            'data' => [
                ['context' => 'Test context 1', 'ftime' => '2023-01-01 10:00:00'],
                ['context' => 'Test context 2', 'ftime' => '2023-01-01 11:00:00'],
            ],
        ];
        
        // 设置模拟行为 - 第一个记录不存在，第二个记录已存在
        $this->logisticsStatusRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnOnConsecutiveCalls(
                null,
                new LogisticsStatus()
            );
        
        // 期望调用实体管理器来保存第一个记录（第二个已存在）
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($status) use ($number) {
                return $status instanceof LogisticsStatus
                    && $status->getNumber() === $number
                    && $status->getSn() === '123456789'
                    && $status->getContext() === 'Test context 1'
                    && $status->getFtime() === '2023-01-01 10:00:00'
                    && $status->getState() === LogisticsStateEnum::SIGN
                    && $status->getCompanyCode() === 'test_code'
                    && $status->getFlag() === md5('Test context 1');
            }));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试方法
        $this->service->syncStatusToDb($number, $apiResponse);
    }
    
    public function testSubscribeWhenNotAlreadySubscribed(): void
    {
        // 创建测试所需数据
        $account = new Account();
        $account->setCustomer('test_customer');
        $account->setSignKey('test_key');
        
        $number = new LogisticsNum();
        $number->setCompany('test_company');
        $number->setNumber('123456789');
        $number->setPhone('13800138000');
        $number->setAccount($account);
        $number->setSubscribed(false);
        
        // 设置回调URL生成
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('kuaidi100-sync-logistics', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/callback');
        
        // 验证API订阅请求构建
        $this->apiService->expects($this->once())
            ->method('request')
            ->willReturnCallback(function ($request) {
                $this->assertInstanceOf(PollRequest::class, $request);
                $this->assertEquals('test_company', $request->getCom());
                $this->assertEquals('123456789', $request->getNum());
                $this->assertEquals('13800138000', $request->getPhone());
                $this->assertEquals('https://example.com/callback', $request->getCallbackUrl());
                
                return ['result' => 'success'];
            });
        
        // 期望调用实体管理器来保存订阅状态
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($num) {
                return $num instanceof LogisticsNum && $num->isSubscribed() === true;
            }));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试方法
        $this->service->subscribe($number);
        
        // 验证订阅状态已更新
        $this->assertTrue($number->isSubscribed());
    }
    
    public function testSubscribeWhenAlreadySubscribed(): void
    {
        // 创建测试所需数据
        $number = new LogisticsNum();
        $number->setSubscribed(true);
        
        // API服务不应被调用
        $this->apiService->expects($this->never())
            ->method('request');
        
        // 实体管理器不应被调用
        $this->entityManager->expects($this->never())
            ->method('persist');
        $this->entityManager->expects($this->never())
            ->method('flush');
        
        // 执行测试方法
        $this->service->subscribe($number);
    }
    
    public function testQueryAndSyncWithMissingFields(): void
    {
        // 创建测试所需数据，但缺少必要字段
        $number = new LogisticsNum();
        // 不设置company，保留空值，会导致RuntimeException
        
        // 期望抛出runtime异常而非TypeError
        $this->expectException(\RuntimeException::class);
        
        // 执行测试方法
        $this->service->queryAndSync($number);
    }
} 