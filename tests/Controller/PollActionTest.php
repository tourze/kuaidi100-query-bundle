<?php

namespace Kuaidi100QueryBundle\Tests\Controller;

use Kuaidi100QueryBundle\Controller\PollAction;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * 测试PollAction的基本功能
 */
class PollActionTest extends TestCase
{
    private PollAction $action;
    private KuaidiCompanyRepository|MockObject $companyRepository;
    private Kuaidi100Service|MockObject $service;
    private AccountRepository|MockObject $accountRepository;
    private UrlGeneratorInterface|MockObject $urlGenerator;
    
    public function testInvokeWithValidData(): void
    {
        $account = $this->createMock(Account::class);
        $company = $this->createMock(KuaidiCompany::class);
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

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('kuaidi100-sync-logistics', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://example.com/callback');

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
        $this->companyRepository = $this->createMock(KuaidiCompanyRepository::class);
        $this->service = $this->createMock(Kuaidi100Service::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->action = new PollAction(
            $this->companyRepository,
            $this->service,
            $this->accountRepository,
            $this->urlGenerator
        );
        
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