<?php

namespace Kuaidi100QueryBundle\Tests\Service;

use Kuaidi100QueryBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 测试AttributeControllerLoader的基本功能
 *
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 无需特殊设置
    }

    public function testServiceCanBeInstantiated(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $loader);
    }

    public function testServiceImplementsLoaderInterface(): void
    {
        $reflection = new \ReflectionClass(AttributeControllerLoader::class);
        $interfaces = $reflection->getInterfaceNames();

        $this->assertContains('Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface', $interfaces);
    }

    public function testAutoload(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $routes = $loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $routes);
        $this->assertGreaterThan(0, $routes->count(), 'AttributeControllerLoader 应该至少加载一个路由');
    }

    public function testLoad(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $routes = $loader->load('Kuaidi100QueryBundle\Controller', 'attribute');

        $this->assertInstanceOf(RouteCollection::class, $routes);
        // 验证加载的路由包含预期的控制器路由
        $routeNames = array_keys($routes->all());
        $this->assertNotEmpty($routeNames, 'load 方法应该加载控制器路由');
    }

    public function testSupports(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);

        // 测试支持的资源类型
        $this->assertTrue($loader->supports('.', 'kuaidi100_controller'));
        $this->assertTrue($loader->supports('anything', 'kuaidi100_controller'));

        // 测试不支持的资源类型
        $this->assertFalse($loader->supports('some/file.yml', 'yaml'));
        $this->assertFalse($loader->supports('some/file.xml', 'xml'));
        $this->assertFalse($loader->supports('.', 'attribute'));
        $this->assertFalse($loader->supports('SomeController', null));
    }
}
