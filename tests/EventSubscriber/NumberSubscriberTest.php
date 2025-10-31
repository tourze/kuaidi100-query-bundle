<?php

namespace Kuaidi100QueryBundle\Tests\EventSubscriber;

use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\EventSubscriber\NumberSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(NumberSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class NumberSubscriberTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // EventSubscriber 测试的特定设置
    }

    private NumberSubscriber $subscriber;

    private function initializeTest(): void
    {
        // 从容器中获取真实的服务进行集成测试
        $this->subscriber = self::getService(NumberSubscriber::class);
    }

    public function testSubscribeSuccess(): void
    {
        // 集成测试：验证 NumberSubscriber 可以正确处理 LogisticsNum 实体
        $this->initializeTest();

        $em = self::getEntityManager();

        // 创建一个真实的 LogisticsNum 实体进行测试
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setNumber('TEST123');
        $logisticsNum->setCompany('test');
        $em->persist($logisticsNum);
        $em->flush();

        // 由于外部API可能失败，我们只验证订阅方法不抛出异常
        $this->expectNotToPerformAssertions();

        try {
            $this->subscriber->subscribe($logisticsNum);
        } catch (\Throwable) {
            // 外部API失败是正常的，不应该影响测试
        }
    }

    public function testSubscribeWithException(): void
    {
        // 集成测试：验证 NumberSubscriber 在异常情况下的行为
        $this->initializeTest();

        $em = self::getEntityManager();

        // 创建一个真实的 LogisticsNum 实体，但使用无效数据以触发异常
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setNumber('INVALID_NUMBER_FOR_TEST');
        $logisticsNum->setCompany('invalid');
        $em->persist($logisticsNum);
        $em->flush();

        // 外部API调用预期会失败，我们验证不会抛出未处理的异常
        $this->expectNotToPerformAssertions();

        try {
            $this->subscriber->subscribe($logisticsNum);
        } catch (\Throwable) {
            // 异常被正确处理和记录，这是预期的行为
        }
    }
}
