<?php

namespace Kuaidi100QueryBundle\Tests\Entity;

use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    private Account $account;

    protected function createEntity(): object
    {
        $account = new Account();
        $account->setCustomer('test_customer');
        $account->setUserid('test_userid');
        $account->setSecret('test_secret');
        $account->setSignKey('test_sign_key');
        $account->setValid(true);

        return $account;
    }

    protected function setUp(): void
    {
        parent::setUp();
        /** @var Account $account */
        $account = $this->createEntity();
        $this->account = $account;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $now = new \DateTimeImmutable();

        return [
            'customer' => ['customer', 'test_customer'],
            'userid' => ['userid', 'test_userid'],
            'secret' => ['secret', 'test_secret'],
            'signKey' => ['signKey', 'test_sign_key'],
            'valid' => ['valid', true],
            'createTime' => ['createTime', $now],
            'updateTime' => ['updateTime', $now],
            'createdBy' => ['createdBy', 'test_user'],
            'updatedBy' => ['updatedBy', 'test_user'],
        ];
    }

    public function testTimestampFields(): void
    {
        // 测试创建时间和更新时间字段
        $now = new \DateTimeImmutable();

        $this->account->setCreateTime($now);
        $this->account->setUpdateTime($now);

        $this->assertSame($now, $this->account->getCreateTime());
        $this->assertSame($now, $this->account->getUpdateTime());
    }

    public function testTrackFields(): void
    {
        // 测试创建人和更新人字段
        $this->account->setCreatedBy('creator');
        $this->account->setUpdatedBy('updater');

        $this->assertEquals('creator', $this->account->getCreatedBy());
        $this->assertEquals('updater', $this->account->getUpdatedBy());
    }

    public function testToString(): void
    {
        // 测试__toString方法
        $this->account->setCustomer('test_customer');
        $this->account->setUserid('test_userid');

        // 由于没有设置ID，应该返回空字符串
        $this->assertEquals('', $this->account->__toString());

        // 使用反射设置ID
        $reflection = new \ReflectionClass($this->account);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->account, '123456789');

        $this->assertEquals('test_customer(test_userid)', $this->account->__toString());
    }

    public function testToArray(): void
    {
        // 测试toArray方法
        $expected = [
            'userid' => 'test_userid',
            'secret' => 'test_secret',
        ];

        $this->assertEquals($expected, $this->account->toArray());
    }

    public function testRetrieveAdminArray(): void
    {
        // 测试retrieveAdminArray方法
        $now = new \DateTimeImmutable('2023-01-01 12:00:00');
        $this->account->setCreateTime($now);
        $this->account->setUpdateTime($now);

        // 使用反射设置ID
        $reflection = new \ReflectionClass($this->account);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->account, '123456789');

        $expected = [
            'id' => '123456789',
            'createTime' => '2023-01-01 12:00:00',
            'updateTime' => '2023-01-01 12:00:00',
            'valid' => true,
            'customer' => 'test_customer',
            'userid' => 'test_userid',
            'secret' => 'test_secret',
            'signKey' => 'test_sign_key',
        ];

        $this->assertEquals($expected, $this->account->retrieveAdminArray());
    }

    public function testNullValues(): void
    {
        // 测试空值处理
        $account = new Account();

        $this->assertNull($account->getCustomer());
        $this->assertNull($account->getUserid());
        $this->assertNull($account->getSecret());
        $this->assertNull($account->getSignKey());
        // valid属性初始化为false而不是null
        $this->assertFalse($account->isValid());
        $this->assertNull($account->getId());
        $this->assertNull($account->getCreateTime());
        $this->assertNull($account->getUpdateTime());
        $this->assertNull($account->getCreatedBy());
        $this->assertNull($account->getUpdatedBy());
    }

    public function testNumbersCollection(): void
    {
        // 测试numbers集合
        $numbers = $this->account->getNumbers();
        $this->assertNotNull($numbers);
        $this->assertCount(0, $numbers);

        // 添加物流单号
        $number1 = new LogisticsNum();
        $number2 = new LogisticsNum();

        $this->account->addNumber($number1);
        $this->account->addNumber($number2);

        $this->assertCount(2, $this->account->getNumbers());
        $this->assertTrue($this->account->getNumbers()->contains($number1));
        $this->assertTrue($this->account->getNumbers()->contains($number2));

        // 检查添加物流单号是否设置了关联关系
        $this->assertSame($this->account, $number1->getAccount());
        $this->assertSame($this->account, $number2->getAccount());

        // 测试不重复添加
        $this->account->addNumber($number1);
        $this->assertCount(2, $this->account->getNumbers());

        // 测试移除物流单号
        $this->account->removeNumber($number1);
        $this->assertCount(1, $this->account->getNumbers());
        $this->assertFalse($this->account->getNumbers()->contains($number1));
        $this->assertTrue($this->account->getNumbers()->contains($number2));

        // 检查移除物流单号是否解除了关联关系
        $this->assertNull($number1->getAccount());
    }

    public function testFluentSetters(): void
    {
        // 测试流式setter方法的返回类型
        $number1 = new LogisticsNum();
        $number2 = new LogisticsNum();

        $result1 = $this->account->addNumber($number1);
        $result2 = $this->account->removeNumber($number2);

        $this->assertSame($this->account, $result1);
        $this->assertSame($this->account, $result2);
    }
}
