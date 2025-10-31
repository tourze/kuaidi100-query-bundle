<?php

namespace Kuaidi100QueryBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Service\LogisticsService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[AsEntityListener(event: Events::postPersist, method: 'subscribe', entity: LogisticsNum::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'subscribe', entity: LogisticsNum::class)]
#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'kuaidi100_query')]
readonly class NumberSubscriber
{
    public function __construct(
        private LogisticsService $logisticsService,
        private LoggerInterface $logger,
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
