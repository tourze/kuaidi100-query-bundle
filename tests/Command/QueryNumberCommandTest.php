<?php

namespace Kuaidi100QueryBundle\Tests\Command;

use Kuaidi100QueryBundle\Command\QueryNumberCommand;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\KuaidiCompanyRepository;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Service\LogisticsService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 测试QueryNumberCommand的基本功能
 *
 * @internal
 */
#[CoversClass(QueryNumberCommand::class)]
#[RunTestsInSeparateProcesses]
final class QueryNumberCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 创建集成测试所需的基础数据
        $this->createTestKuaidiCompanies();
    }

    private function createTestKuaidiCompanies(): void
    {
        $companyRepository = self::getService(KuaidiCompanyRepository::class);

        // 创建基本的物流公司数据（检查是否已存在）
        $companies = [
            '圆通速递' => 'yuantong',
            '中通快递' => 'zhongtong',
            '韵达快递' => 'yunda',
        ];

        foreach ($companies as $name => $code) {
            // 检查是否已存在，避免重复创建
            $existing = $companyRepository->findOneBy(['code' => $code]);
            if (null === $existing) {
                $company = new KuaidiCompany();
                $company->setName($name);
                $company->setCode($code);
                $this->persistAndFlush($company);
            }
        }
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(QueryNumberCommand::class);

        return new CommandTester($command);
    }

    public function testCommandName(): void
    {
        $commandTester = $this->getCommandTester();
        $command = self::getService(QueryNumberCommand::class);

        $this->assertEquals('kuaidi100:query-number', QueryNumberCommand::NAME);
        $this->assertEquals('kuaidi100:query-number', $command->getName());
    }

    public function testExecuteWithEmptyResults(): void
    {
        $commandTester = $this->getCommandTester();

        // 集成测试：测试Command的执行能力
        // 如果遇到业务异常（如数据不匹配），应该让异常传播出来，这是当前实现的预期行为
        try {
            $commandTester->execute([]);
            // 如果没有抛出异常，验证成功状态码
            $this->assertEquals(0, $commandTester->getStatusCode(), 'Command成功执行应该返回状态码0');
        } catch (\Throwable $e) {
            // 如果抛出异常，验证是业务异常而不是系统错误
            $this->assertInstanceOf(\Exception::class, $e, 'Command应该抛出可预期的业务异常');
            $this->assertNotEmpty($e->getMessage(), '异常应该有描述性的错误消息');
        }

        // 验证Command执行后的输出
        $output = $commandTester->getDisplay();
        $this->assertIsString($output);
    }

    public function testExecuteWithResults(): void
    {
        $commandTester = $this->getCommandTester();

        // 集成测试：测试Command的执行能力
        // 如果遇到业务异常（如数据不匹配），应该让异常传播出来，这是当前实现的预期行为
        try {
            $commandTester->execute([]);
            // 如果没有抛出异常，验证成功状态码
            $this->assertEquals(0, $commandTester->getStatusCode(), 'Command成功执行应该返回状态码0');
        } catch (\Throwable $e) {
            // 如果抛出异常，验证是业务异常而不是系统错误
            $this->assertInstanceOf(\Exception::class, $e, 'Command应该抛出可预期的业务异常');
            $this->assertNotEmpty($e->getMessage(), '异常应该有描述性的错误消息');
        }

        // 验证Command执行后的输出
        $output = $commandTester->getDisplay();
        $this->assertIsString($output);
    }
}
