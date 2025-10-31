<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'dev')]
class LogisticsStatusFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $logisticsNum = $this->getReference(LogisticsNumFixtures::LOGISTICS_NUM_REFERENCE, LogisticsNum::class);

        $status1 = new LogisticsStatus();
        $status1->setNumber($logisticsNum);
        $status1->setFtime('2024-01-01 10:00:00');
        $status1->setContext('【北京市】快件已在【北京朝阳三部】装车，准备发往下一站');
        $status1->setAreaCenter('北京转运中心');
        $status1->setSn('status_001');
        $status1->setCompanyCode('yuantong');
        $status1->setFlag('status_001_flag');

        $status2 = new LogisticsStatus();
        $status2->setNumber($logisticsNum);
        $status2->setFtime('2024-01-01 15:30:00');
        $status2->setContext('【上海市】快件已到达【上海浦东新区公司】');
        $status2->setAreaCenter('上海转运中心');
        $status2->setSn('status_002');
        $status2->setCompanyCode('yuantong');
        $status2->setFlag('status_002_flag');

        $manager->persist($status1);
        $manager->persist($status2);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LogisticsNumFixtures::class,
        ];
    }
}
