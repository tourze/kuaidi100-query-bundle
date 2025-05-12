<?php

namespace Kuaidi100QueryBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Service\LogisticsService;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::postPersist, method: 'subscribe', entity: LogisticsNum::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'subscribe', entity: LogisticsNum::class)]
class NumberSubscriber
{
    public function __construct(
        private readonly LogisticsService $logisticsService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function subscribe(LogisticsNum $number): void
    {
        try {
            $this->logisticsService->subscribe($number);
        } catch (\Throwable $exception) {
            $this->logger->error('订阅快递动态失败', [
                'number' => $number,
                'exception' => $exception,
            ]);
        }
    }
}
