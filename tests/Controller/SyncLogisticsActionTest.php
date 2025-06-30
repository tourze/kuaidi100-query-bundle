<?php

namespace Kuaidi100QueryBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Controller\SyncLogisticsAction;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Service\LogisticsService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * 测试SyncLogisticsAction的基本功能
 */
class SyncLogisticsActionTest extends TestCase
{
    private SyncLogisticsAction $action;
    private LogisticsNumRepository|MockObject $logisticsNumRepository;
    private EntityManagerInterface|MockObject $entityManager;
    private LogisticsService|MockObject $service;
    
    public function testInvokeWithExistingLogisticsNum(): void
    {
        $logisticsNum = $this->createMock(LogisticsNum::class);

        $request = new Request([
            'params' => [
                'nu' => '1234567890',
                'com' => 'yuantong',
                'data' => []
            ],
            'sign' => 'test_sign'
        ]);

        $this->logisticsNumRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['number' => '1234567890'])
            ->willReturn($logisticsNum);

        $this->service
            ->expects($this->once())
            ->method('syncStatusToDb')
            ->with($logisticsNum, ['nu' => '1234567890', 'com' => 'yuantong', 'data' => []]);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = ($this->action)($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
    public function testInvokeWithNewLogisticsNum(): void
    {
        $request = new Request([
            'params' => [
                'nu' => '1234567890',
                'com' => 'yuantong',
                'data' => []
            ],
            'nu' => '1234567890',
            'com' => 'yuantong',
            'sign' => 'test_sign'
        ]);

        $this->logisticsNumRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['number' => '1234567890'])
            ->willReturn(null);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(LogisticsNum::class));

        $this->service
            ->expects($this->once())
            ->method('syncStatusToDb');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = ($this->action)($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
    protected function setUp(): void
    {
        $this->logisticsNumRepository = $this->createMock(LogisticsNumRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->service = $this->createMock(LogisticsService::class);

        $this->action = new SyncLogisticsAction(
            $this->logisticsNumRepository,
            $this->entityManager,
            $this->service
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