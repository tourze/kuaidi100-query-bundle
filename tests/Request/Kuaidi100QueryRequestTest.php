<?php

namespace Kuaidi100QueryBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Request\Kuaidi100QueryRequest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(Kuaidi100QueryRequest::class)]
final class Kuaidi100QueryRequestTest extends RequestTestCase
{
    private Kuaidi100QueryRequest $request;

    private Account $account;

    protected function setUp(): void
    {
        // 创建测试对象
        $this->account = new Account();
        $this->account->setCustomer('test_customer');
        $this->account->setUserid('test_userid');
        $this->account->setSecret('test_secret');
        $this->account->setSignKey('test_sign_key');
        $this->account->setValid(true);

        // 创建快递查询请求对象
        $this->request = new Kuaidi100QueryRequest();
        $this->request->setAccount($this->account);
        $this->request->setCom('test_com');
        $this->request->setNum('123456789');
        $this->request->setPhone('13800138000');
    }

    public function testGetRequestPath(): void
    {
        // 测试API请求路径是否正确
        $this->assertEquals('https://poll.kuaidi100.com/poll/query.do', $this->request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        // 测试请求选项是否正确生成
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options, 'getRequestOptions() should not return null');
        $this->assertIsArray($options, 'getRequestOptions() should return an array');
        $this->assertArrayHasKey('customer', $options);
        $this->assertArrayHasKey('param', $options);
        $this->assertEquals('test_customer', $options['customer']);

        // 验证param字段是否包含必要的参数
        $this->assertIsString($options['param']);
        /** @var array<string, mixed> $param */
        $param = json_decode($options['param'], true);
        $this->assertIsArray($param);
        $this->assertIsString($param['com']);
        $this->assertEquals('test_com', strtolower($param['com']));
        $this->assertEquals('123456789', $param['num']);
        $this->assertEquals('13800138000', $param['phone']);
        $this->assertEquals('1', $param['resultv2']); // 验证行政区域解析选项
    }

    public function testGetSing(): void
    {
        // 测试签名计算是否正确
        $expectedSignStr = $this->request->getParam() . $this->account->getSignKey() . $this->account->getCustomer();
        $expectedSign = strtoupper(md5($expectedSignStr));

        $this->assertEquals($expectedSign, $this->request->getSing());
    }

    public function testGetSingStr(): void
    {
        // 测试签名字符串是否正确
        $expectedSignStr = $this->request->getParam() . $this->account->getSignKey() . $this->account->getCustomer();

        $this->assertEquals($expectedSignStr, $this->request->getSingStr());
    }

    public function testGetParam(): void
    {
        // 测试参数JSON是否正确生成
        $expectedParam = json_encode([
            'com' => 'test_com',
            'num' => '123456789',
            'phone' => '13800138000',
            'resultv2' => '1',
        ], JSON_UNESCAPED_UNICODE);

        $this->assertEquals($expectedParam, $this->request->getParam());
    }

    public function testCacheKey(): void
    {
        // 测试缓存键生成是否正确
        $this->assertEquals('KUAIDI100_QUERY_123456789', $this->request->getCacheKey());
    }

    public function testCacheDuration(): void
    {
        // 测试缓存持续时间是否为1小时
        $this->assertEquals(60 * 60, $this->request->getCacheDuration());
    }

    public function testGettersAndSetters(): void
    {
        // 测试getter和setter方法
        $this->assertEquals('test_com', $this->request->getCom());
        $this->assertEquals('123456789', $this->request->getNum());
        $this->assertEquals('13800138000', $this->request->getPhone());

        // 测试修改值
        $this->request->setCom('new_com');
        $this->request->setNum('987654321');
        $this->request->setPhone('13900139000');

        $this->assertEquals('new_com', $this->request->getCom());
        $this->assertEquals('987654321', $this->request->getNum());
        $this->assertEquals('13900139000', $this->request->getPhone());
    }
}
