<?php

namespace Kuaidi100QueryBundle\Tests\Service;

use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 测试Kuaidi100Service的基本功能
 *
 * @internal
 */
#[CoversClass(Kuaidi100Service::class)]
#[RunTestsInSeparateProcesses]
final class Kuaidi100ServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 实现抽象方法，这里不需要额外的设置
    }

    private Kuaidi100Service $service;

    private function initializeTest(): void
    {
        // 使用 getService() 方法代替 getContainer()->get() 进行类型安全的服务获取
        $this->service = self::getService(Kuaidi100Service::class);
    }

    public function testGetBaseUrl(): void
    {
        $this->initializeTest();
        // 测试基础URL是否为空
        $this->assertEquals('', $this->service->getBaseUrl());
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->initializeTest();
        $this->assertNotNull($this->service);
    }
}
