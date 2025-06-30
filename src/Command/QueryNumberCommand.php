<?php

namespace Kuaidi100QueryBundle\Command;

use Carbon\CarbonImmutable;
use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use Kuaidi100QueryBundle\Service\LogisticsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '* * * * *')]
#[AsCommand(name: self::NAME, description: '查询实时快递状态')]
class QueryNumberCommand extends LockableCommand
{
    public const NAME = 'kuaidi100:query-number';
    public function __construct(
        private readonly LogisticsNumRepository $numberRepository,
        private readonly LogisticsService $logisticsService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->numberRepository->findNeedSyncList(CarbonImmutable::now()) as $item) {
            $this->logisticsService->queryAndSync($item);
        }

        return Command::SUCCESS;
    }
}
