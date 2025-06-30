<?php

namespace Kuaidi100QueryBundle\Tests\Command;

use Kuaidi100QueryBundle\Command\SetSubscribedCommand;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Service\LogisticsService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 测试SetSubscribedCommand的基本功能
 */
class SetSubscribedCommandTest extends TestCase
{
    private SetSubscribedCommand $command;
    private LogisticsNumRepository|MockObject $numberRepository;
    private LogisticsService|MockObject $logisticsService;
    
    public function testCommandName(): void
    {
        $this->assertEquals('kuaidi100:set-subscribed', SetSubscribedCommand::NAME);
        $this->assertEquals('kuaidi100:set-subscribed', $this->command->getName());
    }
    
    public function testExecuteWithEmptyResults(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $this->numberRepository
            ->expects($this->once())
            ->method('findUnsubscribedList')
            ->willReturn([]);

        $this->logisticsService
            ->expects($this->never())
            ->method('subscribe');

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
            ->method('findUnsubscribedList')
            ->willReturn([$logisticsNum1, $logisticsNum2]);

        $this->logisticsService
            ->expects($this->exactly(2))
            ->method('subscribe')
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
        $this->command = new SetSubscribedCommand($this->numberRepository, $this->logisticsService);
    }
} 