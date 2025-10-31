<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use Kuaidi100QueryBundle\Entity\LogisticsNum;

final class TestDataFactory
{
    private static ?Account $defaultAccount = null;

    /** @var array<string, KuaidiCompany> */
    private static array $companies = [];

    private static bool $initialized = false;

    public static function initialize(EntityManagerInterface $em): void
    {
        if (self::$initialized) {
            return;
        }

        // 创建默认测试账户 - 使用唯一标识符避免冲突
        $uniqueId = uniqid('test_', true);
        self::$defaultAccount = new Account();
        self::$defaultAccount->setSignKey('sign_key_' . $uniqueId);
        self::$defaultAccount->setSecret('secret_' . $uniqueId);
        self::$defaultAccount->setCustomer('customer_' . $uniqueId);
        self::$defaultAccount->setUserid('userid_' . $uniqueId);
        self::$defaultAccount->setValid(true);
        $em->persist(self::$defaultAccount);

        // 批量创建快递公司
        $companyData = [
            ['圆通速递', 'yuantong'],
            ['申通快递', 'shentong'],
            ['中通快递', 'zhongtong'],
            ['韵达快递', 'yunda'],
            ['顺丰', 'shunfeng'],
            ['邮政EMS', 'ems'],
        ];

        foreach ($companyData as [$name, $code]) {
            $company = new KuaidiCompany();
            $company->setName($name . '_' . $uniqueId); // 确保name也唯一
            $company->setCode($code . '_' . $uniqueId); // 确保code也唯一
            $em->persist($company);
            self::$companies[$code] = $company;
        }

        $em->flush();
        self::$initialized = true;
    }

    public static function getDefaultAccount(): ?Account
    {
        return self::$defaultAccount;
    }

    public static function getCompanyByCode(string $code): ?KuaidiCompany
    {
        return self::$companies[$code] ?? null;
    }

    public static function createLogisticsNum(
        string $company = 'yuantong',
        ?string $number = null,
        ?Account $account = null,
    ): LogisticsNum {
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany($company);
        $logisticsNum->setNumber($number ?? uniqid($company . '_'));

        if (null !== $account) {
            $logisticsNum->setAccount($account);
        } elseif (null !== self::$defaultAccount) {
            $logisticsNum->setAccount(self::$defaultAccount);
        }

        return $logisticsNum;
    }

    public static function createAccount(string $suffix = ''): Account
    {
        $account = new Account();
        $account->setSignKey('sign_key_' . uniqid() . $suffix);
        $account->setSecret('secret_' . uniqid() . $suffix);
        $account->setCustomer('customer_' . uniqid() . $suffix);
        $account->setUserid('userid_' . uniqid() . $suffix);
        $account->setValid(true);

        return $account;
    }

    public static function reset(): void
    {
        self::$defaultAccount = null;
        self::$companies = [];
        self::$initialized = false;
    }
}
