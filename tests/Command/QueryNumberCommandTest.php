<?php

namespace Kuaidi100QueryBundle\Tests\Command;

use Carbon\CarbonImmutable;
use Kuaidi100QueryBundle\Command\QueryNumberCommand;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Service\LogisticsService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 测试QueryNumberCommand的基本功能
 */
class QueryNumberCommandTest extends TestCase
{
    private QueryNumberCommand $command;
    private LogisticsNumRepository|MockObject $numberRepository;
    private LogisticsService|MockObject $logisticsService;
    
    public function testCommandName(): void
    {
        $this->assertEquals('kuaidi100:query-number', QueryNumberCommand::NAME);
        $this->assertEquals('kuaidi100:query-number', $this->command->getName());
    }
    
    public function testExecuteWithEmptyResults(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $this->numberRepository
            ->expects($this->once())
            ->method('findNeedSyncList')
            ->with($this->isInstanceOf(CarbonImmutable::class))
            ->willReturn([]);

        $this->logisticsService
            ->expects($this->never())
            ->method('queryAndSync');

        $result = $this->command->run($input, $output);
        $this->assertEquals(0, $result);
    }
    
    public function testExecuteWithResults(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $logisticsNum1 = $this->createMock(LogisticsNum::class);
        $logisticsNum2 = $this->createMock(LogisticsNum::class);

        $this->numberRepository
            ->expects($this->once())
            ->method('findNeedSyncList')
            ->with($this->isInstanceOf(CarbonImmutable::class))
            ->willReturn([$logisticsNum1, $logisticsNum2]);

        $this->logisticsService
            ->expects($this->exactly(2))
            ->method('queryAndSync')
            ->willReturnCallback(function ($logisticsNum) use ($logisticsNum1, $logisticsNum2) {
                $this->assertContains($logisticsNum, [$logisticsNum1, $logisticsNum2]);
            });

        $result = $this->command->run($input, $output);
        $this->assertEquals(0, $result);
    }
    
    protected function setUp(): void
    {
        $this->numberRepository = $this->createMock(LogisticsNumRepository::class);
        $this->logisticsService = $this->createMock(LogisticsService::class);
        $this->command = new QueryNumberCommand($this->numberRepository, $this->logisticsService);
    }
} 