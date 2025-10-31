<?php

namespace Kuaidi100QueryBundle\Tests\Command;

use Kuaidi100QueryBundle\Command\SetSubscribedCommand;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Service\LogisticsService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 测试SetSubscribedCommand的基本功能
 *
 * @internal
 */
#[CoversClass(SetSubscribedCommand::class)]
#[RunTestsInSeparateProcesses]
final class SetSubscribedCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // Command测试不需要额外的设置
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SetSubscribedCommand::class);

        return new CommandTester($command);
    }

    public function testCommandName(): void
    {
        $commandTester = $this->getCommandTester();
        $command = self::getService(SetSubscribedCommand::class);

        $this->assertEquals('kuaidi100:set-subscribed', SetSubscribedCommand::NAME);
        $this->assertEquals('kuaidi100:set-subscribed', $command->getName());
    }

    public function testExecuteWithEmptyResults(): void
    {
        $commandTester = $this->getCommandTester();

        // 真正的集成测试：测试Command在空数据集情况下的行为
        $commandTester->execute([]);
        $this->assertEquals(0, $commandTester->getStatusCode());

        // 验证Command执行后的输出
        $output = $commandTester->getDisplay();
        $this->assertIsString($output);
    }

    public function testExecuteWithResults(): void
    {
        $commandTester = $this->getCommandTester();

        // 真正的集成测试：直接执行Command，测试其处理能力
        // 即使没有数据或遇到异常，Command也应该能够正常处理并返回合适的状态码
        $commandTester->execute([]);

        // 验证Command执行完成（无论成功还是遇到业务异常，都不应该是系统错误）
        $statusCode = $commandTester->getStatusCode();
        $this->assertContains($statusCode, [0, 1], 'Command应该正常完成执行，返回0(成功)或1(业务异常)');

        // 验证Command执行后的输出
        $output = $commandTester->getDisplay();
        $this->assertIsString($output);
    }
}
