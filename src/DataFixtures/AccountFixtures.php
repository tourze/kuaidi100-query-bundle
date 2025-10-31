<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Kuaidi100QueryBundle\Entity\Account;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'dev')]
class AccountFixtures extends Fixture
{
    public const ACCOUNT_REFERENCE = 'account';

    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setSignKey('test_sign_key_123');
        $account->setSecret('test_secret_456');
        $account->setCustomer('test_customer');
        $account->setUserid('100001');
        $account->setValid(true);

        $manager->persist($account);
        $manager->flush();

        $this->setReference(self::ACCOUNT_REFERENCE, $account);
    }
}
