<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'dev')]
class LogisticsNumFixtures extends Fixture implements DependentFixtureInterface
{
    public const LOGISTICS_NUM_REFERENCE = 'logistics-num';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $logisticsNum = new LogisticsNum();
        $logisticsNum->setAccount($account);
        $logisticsNum->setCompany('yuantong');
        $logisticsNum->setNumber('YT1234567890');
        $logisticsNum->setPhoneNumber('13800138000');
        $logisticsNum->setFromCity('北京市朝阳区');
        $logisticsNum->setToCity('上海市浦东新区');
        $logisticsNum->setSubscribed(false);

        $manager->persist($logisticsNum);
        $manager->flush();

        $this->setReference(self::LOGISTICS_NUM_REFERENCE, $logisticsNum);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
