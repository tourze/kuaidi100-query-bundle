<?php

namespace Kuaidi100QueryBundle\Tests\Service;

use Kuaidi100QueryBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\TestCase;

/**
 * 测试AttributeControllerLoader的基本功能
 */
class AttributeControllerLoaderTest extends TestCase
{
    public function testServiceCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(AttributeControllerLoader::class));
    }
    
    public function testServiceImplementsLoaderInterface(): void
    {
        $reflection = new \ReflectionClass(AttributeControllerLoader::class);
        $interfaces = $reflection->getInterfaceNames();
        
        $this->assertContains('Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface', $interfaces);
    }
} 