<?php

namespace Kuaidi100QueryBundle\Tests\Request;

use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Request\PollRequest;
use PHPUnit\Framework\TestCase;

class PollRequestTest extends TestCase
{
    private PollRequest $request;
    private Account $account;
    
    protected function setUp(): void
    {
        // 创建模拟的Account对象
        $this->account = new Account();
        $this->account->setCustomer('test_customer');
        $this->account->setUserid('test_userid');
        $this->account->setSecret('test_secret');
        $this->account->setSignKey('test_sign_key');
        
        // 创建订阅请求对象
        $this->request = new PollRequest();
        $this->request->setAccount($this->account);
        $this->request->setCom('test_com');
        $this->request->setNum('123456789');
        $this->request->setPhone('13800138000');
        $this->request->setCallbackUrl('https://example.com/callback');
    }
    
    public function testGetRequestPath(): void
    {
        // 测试API请求路径是否正确
        $this->assertEquals('https://poll.kuaidi100.com/poll', $this->request->getRequestPath());
    }
    
    public function testGetRequestOptions(): void
    {
        // 测试请求选项是否正确生成
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('param', $options);
        
        // 解码JSON参数并验证
        $param = json_decode($options['param'], true);
        $this->assertEquals(strtolower('test_com'), $param['company']);
        $this->assertEquals('123456789', $param['number']);
        $this->assertEquals('test_sign_key', $param['key']);
        
        // 验证parameters子参数
        $this->assertArrayHasKey('parameters', $param);
        $this->assertEquals(4, $param['parameters']['resultv2']);
        $this->assertEquals('https://example.com/callback', $param['parameters']['callbackurl']);
        $this->assertEquals('13800138000', $param['parameters']['phone']);
    }
    
    public function testGettersAndSetters(): void
    {
        // 测试getter和setter方法
        $this->assertEquals('test_com', $this->request->getCom());
        $this->assertEquals('123456789', $this->request->getNum());
        $this->assertEquals('13800138000', $this->request->getPhone());
        $this->assertEquals('https://example.com/callback', $this->request->getCallbackUrl());
        
        // 测试修改值后的getter
        $this->request->setCom('new_com');
        $this->request->setNum('987654321');
        $this->request->setPhone('13900139000');
        $this->request->setCallbackUrl('https://example.com/new-callback');
        
        $this->assertEquals('new_com', $this->request->getCom());
        $this->assertEquals('987654321', $this->request->getNum());
        $this->assertEquals('13900139000', $this->request->getPhone());
        $this->assertEquals('https://example.com/new-callback', $this->request->getCallbackUrl());
    }
    
    public function testRequestWithEmptyValues(): void
    {
        // 测试使用空值时的行为
        $request = new PollRequest();
        $request->setAccount($this->account);
        
        // 未设置必要参数时的测试，期望抛出Error异常
        $this->expectException(\Error::class);
        $this->expectExceptionMessageMatches('/Typed property .* must not be accessed before initialization/');
        
        $request->getRequestOptions();
    }
    
    public function testRequestWithExtremeValues(): void
    {
        // 测试使用极端值的场景
        $this->request->setCom('');
        $this->request->setNum('');
        $this->request->setPhone('');
        $this->request->setCallbackUrl('');
        
        $options = $this->request->getRequestOptions();
        $param = json_decode($options['param'], true);
        
        // 空值测试验证
        $this->assertEquals('', $param['company']);
        $this->assertEquals('', $param['number']);
        $this->assertEquals('', $param['parameters']['phone']);
        $this->assertEquals('', $param['parameters']['callbackurl']);
    }
} 