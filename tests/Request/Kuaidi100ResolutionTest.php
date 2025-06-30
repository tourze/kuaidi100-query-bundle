<?php

namespace Kuaidi100QueryBundle\Tests\Request;

use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Request\Kuaidi100Resolution;
use PHPUnit\Framework\TestCase;

/**
 * 测试Kuaidi100Resolution请求类的基本功能
 */
class Kuaidi100ResolutionTest extends TestCase
{
    private Kuaidi100Resolution $request;
    
    public function testGettersAndSetters(): void
    {
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
        $account = $this->createMock(Account::class);
        $account->expects($this->any())
            ->method('getSignKey')
            ->willReturn('test_key');

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
        $this->request = new Kuaidi100Resolution();
    }
} 