<?php

namespace Kuaidi100QueryBundle\Tests\Integration\EventSubscriber;

use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\EventSubscriber\NumberSubscriber;
use Kuaidi100QueryBundle\Service\LogisticsService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class NumberSubscriberTest extends TestCase
{
    private LogisticsService $logisticsService;
    private LoggerInterface $logger;
    private NumberSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->logisticsService = $this->createMock(LogisticsService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->subscriber = new NumberSubscriber($this->logisticsService, $this->logger);
    }

    public function testSubscribeSuccess(): void
    {
        $logisticsNum = $this->createMock(LogisticsNum::class);

        $this->logisticsService
            ->expects($this->once())
            ->method('subscribe')
            ->with($logisticsNum);

        $this->logger
            ->expects($this->never())
            ->method('error');

        $this->subscriber->subscribe($logisticsNum);
    }

    public function testSubscribeWithException(): void
    {
        $logisticsNum = $this->createMock(LogisticsNum::class);
        $exception = new \RuntimeException('Test exception');

        $this->logisticsService
            ->expects($this->once())
            ->method('subscribe')
            ->with($logisticsNum)
            ->willThrowException($exception);

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                '订阅快递动态失败',
                [
                    'number' => $logisticsNum,
                    'exception' => $exception,
                ]
            );

        $this->subscriber->subscribe($logisticsNum);
    }
}