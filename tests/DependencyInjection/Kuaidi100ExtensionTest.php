<?php

namespace Kuaidi100QueryBundle\Tests\DependencyInjection;

use Kuaidi100QueryBundle\DependencyInjection\Kuaidi100Extension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * 测试Kuaidi100Extension的基本功能
 */
class Kuaidi100ExtensionTest extends TestCase
{
    private Kuaidi100Extension $extension;
    private ContainerBuilder $container;
    
    public function testLoad(): void
    {
        $this->extension->load([], $this->container);

        // 验证容器构建成功
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }
    
    public function testGetAlias(): void
    {
        $this->assertEquals('kuaidi100', $this->extension->getAlias());
    }
    
    protected function setUp(): void
    {
        $this->extension = new Kuaidi100Extension();
        $this->container = new ContainerBuilder();
    }
} 