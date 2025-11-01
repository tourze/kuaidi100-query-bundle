<?php

namespace Kuaidi100QueryBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Request\Kuaidi100Resolution;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * 测试Kuaidi100Resolution请求类的基本功能
 *
 * @internal
 */
#[CoversClass(Kuaidi100Resolution::class)]
final class Kuaidi100ResolutionTest extends RequestTestCase
{
    private Kuaidi100Resolution $request;

    public function testGettersAndSetters(): void
    {
        // 使用 Account 具体类进行 mock 是必要的，因为：
        // 1. Request 类需要具体的 Account 实体作为依赖
        // 2. Account 实体的类型约束要求传入具体的 Account 实例
        // 3. Mock 在这里只作为占位符，不调用任何实际方法
        $account = $this->createMock(Account::class);

        $this->request->setT('1640995200');
        $this->request->setContent('test content');
        $this->request->setImageUrl('http://example.com/image.jpg');
        $this->request->setPdfUrl('http://example.com/file.pdf');
        $this->request->setAccount($account);

        $this->assertEquals('1640995200', $this->request->getT());
        $this->assertEquals('test content', $this->request->getContent());
        $this->assertEquals('http://example.com/image.jpg', $this->request->getImageUrl());
        $this->assertEquals('http://example.com/file.pdf', $this->request->getPdfUrl());
        $this->assertSame($account, $this->request->getAccount());
    }

    public function testGetRequestPath(): void
    {
        $path = $this->request->getRequestPath();
        $this->assertNotEmpty($path);
        $this->assertStringContainsString('kuaidi100.com', $path);
    }

    public function testGetRequestOptions(): void
    {
        // 使用 Account 具体类进行 mock 是必要的，因为：
        // 1. Request 类需要具体的 Account 实体作为依赖
        // 2. Account 实体的类型约束要求传入具体的 Account 实例
        // 3. Mock 在这里只作为占位符，不调用任何实际方法
        $account = $this->createMock(Account::class);
        $account->expects($this->any())
            ->method('getSignKey')
            ->willReturn('test_key')
        ;

        $this->request->setT('1640995200');
        $this->request->setContent('test content');
        $this->request->setImageUrl('http://example.com/image.jpg');
        $this->request->setPdfUrl('http://example.com/file.pdf');
        $this->request->setAccount($account);

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('param', $options);
        $this->assertArrayHasKey('key', $options);
        $this->assertArrayHasKey('t', $options);
        $this->assertEquals('test_key', $options['key']);
        $this->assertEquals('1640995200', $options['t']);
    }

    public function testDefaultValues(): void
    {
        $request = new Kuaidi100Resolution();

        // 这些字段有默认值，不会是null
        $this->assertEquals('', $request->getContent());
        $this->assertEquals('', $request->getImageUrl());
        $this->assertEquals('', $request->getPdfUrl());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new Kuaidi100Resolution();
    }
}
