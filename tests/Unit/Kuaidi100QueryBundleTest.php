<?php

namespace Kuaidi100QueryBundle\Tests\Unit;

use HttpClientBundle\HttpClientBundle;
use Kuaidi100QueryBundle\Kuaidi100QueryBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class Kuaidi100QueryBundleTest extends TestCase
{
    private Kuaidi100QueryBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new Kuaidi100QueryBundle();
    }

    public function testIsInstanceOfBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testImplementsBundleDependencyInterface(): void
    {
        $this->assertInstanceOf(BundleDependencyInterface::class, $this->bundle);
    }

    public function testGetBundleDependencies(): void
    {
        $dependencies = Kuaidi100QueryBundle::getBundleDependencies();

        $this->assertArrayHasKey(HttpClientBundle::class, $dependencies);
        $this->assertSame(['all' => true], $dependencies[HttpClientBundle::class]);
    }

    public function testGetBundleDependenciesReturnsCorrectStructure(): void
    {
        $dependencies = Kuaidi100QueryBundle::getBundleDependencies();

        foreach ($dependencies as $bundleClass => $environments) {
            $this->assertIsString($bundleClass);
            $this->assertIsArray($environments);
            $this->assertTrue(class_exists($bundleClass) || interface_exists($bundleClass));
        }
    }
}