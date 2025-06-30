<?php

namespace Kuaidi100QueryBundle\Tests\Request;

use Kuaidi100QueryBundle\Request\Kuaidi100AutoNumber;
use PHPUnit\Framework\TestCase;

/**
 * 测试Kuaidi100AutoNumber请求类的基本功能
 */
class Kuaidi100AutoNumberTest extends TestCase
{
    private Kuaidi100AutoNumber $request;
    
    public function testGettersAndSetters(): void
    {
        $this->request->setNum('1234567890');
        $this->request->setKey('test_key');

        $this->assertEquals('1234567890', $this->request->getNum());
        $this->assertEquals('test_key', $this->request->getKey());
    }
    
    public function testGetRequestPath(): void
    {
        $path = $this->request->getRequestPath();
        $this->assertNotEmpty($path);
        $this->assertStringContainsString('kuaidi100.com', $path);
    }
    
    public function testGetRequestOptions(): void
    {
        $this->request->setNum('1234567890');
        $this->request->setKey('test_key');

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('num', $options);
        $this->assertArrayHasKey('key', $options);
        $this->assertEquals('1234567890', $options['num']);
        $this->assertEquals('test_key', $options['key']);
    }
    
    public function testCanInstantiate(): void
    {
        $request = new Kuaidi100AutoNumber();
        $this->assertInstanceOf(Kuaidi100AutoNumber::class, $request);
    }
    
    protected function setUp(): void
    {
        $this->request = new Kuaidi100AutoNumber();
    }
} 