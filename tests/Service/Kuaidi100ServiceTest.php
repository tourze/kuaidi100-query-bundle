<?php

namespace Kuaidi100QueryBundle\Tests\Service;

use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use PHPUnit\Framework\TestCase;

/**
 * 测试Kuaidi100Service的基本功能
 */
class Kuaidi100ServiceTest extends TestCase
{
    private Kuaidi100Service $service;
    
    protected function setUp(): void
    {
        $this->service = new Kuaidi100Service();
    }
    
    public function testGetBaseUrl(): void
    {
        // 测试基础URL是否为空
        $this->assertEquals('', $this->service->getBaseUrl());
    }
    
    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(Kuaidi100Service::class, $this->service);
    }
}