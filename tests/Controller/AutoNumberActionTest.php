<?php

namespace Kuaidi100QueryBundle\Tests\Controller;

use Kuaidi100QueryBundle\Controller\AutoNumberAction;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * 测试AutoNumberAction的基本功能
 */
class AutoNumberActionTest extends TestCase
{
    private AutoNumberAction $action;
    private AccountRepository|MockObject $accountRepository;
    private Kuaidi100Service|MockObject $service;
    
    public function testInvokeWithValidAccount(): void
    {
        $account = $this->createMock(Account::class);
        $account->expects($this->once())
            ->method('getSignKey')
            ->willReturn('test_sign_key');

        $request = new Request(['sn' => '1234567890']);

        $this->accountRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['valid' => true])
            ->willReturn($account);

        $this->service
            ->expects($this->once())
            ->method('request')
            ->willReturn(['result' => 'success']);

        $response = ($this->action)($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
    public function testInvokeWithoutAccount(): void
    {
        $request = new Request(['sn' => '1234567890']);

        $this->accountRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['valid' => true])
            ->willReturn(null);

        $this->service
            ->expects($this->never())
            ->method('request');

        $this->expectException(AccountNotFoundException::class);

        ($this->action)($request);
    }
    
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->service = $this->createMock(Kuaidi100Service::class);
        $this->action = new AutoNumberAction($this->accountRepository, $this->service);
        
        // 设置容器以支持 json() 方法
        $container = new Container();
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->any())
            ->method('serialize')
            ->willReturn(json_encode(['result' => 'success']));
        $container->set('serializer', $serializer);
        $this->action->setContainer($container);
    }
} 