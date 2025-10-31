<?php

namespace Kuaidi100QueryBundle\Tests\Entity;

use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(LogisticsNum::class)]
final class LogisticsNumTest extends AbstractEntityTestCase
{
    private LogisticsNum $logisticsNum;

    protected function setUp(): void
    {
        $this->logisticsNum = new LogisticsNum();
        $this->logisticsNum->setCompany('test_company');
        $this->logisticsNum->setNumber('123456789');
        $this->logisticsNum->setPhoneNumber('13800138000');
    }

    protected function createEntity(): object
    {
        $logisticsNum = new LogisticsNum();
        $logisticsNum->setCompany('test_company');
        $logisticsNum->setNumber('123456789');
        $logisticsNum->setPhoneNumber('13800138000');

        return $logisticsNum;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $now = new \DateTimeImmutable();

        return [
            'company' => ['company', 'test_company'],
            'number' => ['number', '123456789'],
            'phoneNumber' => ['phoneNumber', '13800138000'],
            'fromCity' => ['fromCity', '北京'],
            'toCity' => ['toCity', '上海'],
            'latestStatus' => ['latestStatus', '包裹已签收'],
            'subscribed' => ['subscribed', true],
            'createTime' => ['createTime', $now],
            'updateTime' => ['updateTime', $now],
            'syncTime' => ['syncTime', $now],
        ];
    }

    public function testTimestampFields(): void
    {
        // 测试创建时间和更新时间字段
        $now = new \DateTimeImmutable();

        $this->logisticsNum->setCreateTime($now);
        $this->logisticsNum->setUpdateTime($now);
        $this->logisticsNum->setSyncTime($now);

        $this->assertSame($now, $this->logisticsNum->getCreateTime());
        $this->assertSame($now, $this->logisticsNum->getUpdateTime());
        $this->assertSame($now, $this->logisticsNum->getSyncTime());
    }

    public function testLocationFields(): void
    {
        // 测试出发地和目的地字段
        $this->logisticsNum->setFromCity('北京');
        $this->logisticsNum->setToCity('上海');

        $this->assertEquals('北京', $this->logisticsNum->getFromCity());
        $this->assertEquals('上海', $this->logisticsNum->getToCity());
    }

    public function testStatusFields(): void
    {
        // 测试状态相关字段
        $this->logisticsNum->setLatestStatus('包裹已签收');
        $this->logisticsNum->setSubscribed(true);

        $this->assertEquals('包裹已签收', $this->logisticsNum->getLatestStatus());
        $this->assertTrue($this->logisticsNum->isSubscribed());
    }

    public function testAccountRelationship(): void
    {
        // 测试与Account的关联关系
        $account = new Account();
        $account->setCustomer('test_customer');

        $this->logisticsNum->setAccount($account);

        $this->assertSame($account, $this->logisticsNum->getAccount());
    }

    public function testStatusListCollection(): void
    {
        // 测试状态列表集合
        $statusList = $this->logisticsNum->getStatusList();
        $this->assertNotNull($statusList);
        $this->assertCount(0, $statusList);

        // 添加状态
        $status1 = new LogisticsStatus();
        $status2 = new LogisticsStatus();

        $this->logisticsNum->addStatusList($status1);
        $this->logisticsNum->addStatusList($status2);

        $this->assertCount(2, $this->logisticsNum->getStatusList());
        $this->assertTrue($this->logisticsNum->getStatusList()->contains($status1));
        $this->assertTrue($this->logisticsNum->getStatusList()->contains($status2));

        // 检查添加状态是否设置了关联关系
        $this->assertSame($this->logisticsNum, $status1->getNumber());
        $this->assertSame($this->logisticsNum, $status2->getNumber());

        // 测试不重复添加
        $this->logisticsNum->addStatusList($status1);
        $this->assertCount(2, $this->logisticsNum->getStatusList());

        // 测试移除状态
        $this->logisticsNum->removeStatusList($status1);
        $this->assertCount(1, $this->logisticsNum->getStatusList());
        $this->assertFalse($this->logisticsNum->getStatusList()->contains($status1));
        $this->assertTrue($this->logisticsNum->getStatusList()->contains($status2));

        // 检查移除状态是否解除了关联关系
        $this->assertNull($status1->getNumber());
    }

    public function testNullValues(): void
    {
        // 测试空值处理
        $logisticsNum = new LogisticsNum();

        $this->assertNull($logisticsNum->getCompany());
        $this->assertNull($logisticsNum->getNumber());
        $this->assertNull($logisticsNum->getPhoneNumber());
        $this->assertNull($logisticsNum->getFromCity());
        $this->assertNull($logisticsNum->getToCity());
        $this->assertNull($logisticsNum->getLatestStatus());
        $this->assertNull($logisticsNum->getSyncTime());
        $this->assertNull($logisticsNum->isSubscribed());
        $this->assertNull($logisticsNum->getAccount());
    }

    public function testSetterMethods(): void
    {
        // 测试setter方法
        $this->logisticsNum->setCompany('fluent_company');
        $this->logisticsNum->setNumber('fluent_number');
        $this->logisticsNum->setPhoneNumber('fluent_phone');
        $this->logisticsNum->setFromCity('fluent_from');
        $this->logisticsNum->setToCity('fluent_to');
        $this->logisticsNum->setLatestStatus('fluent_status');
        $this->logisticsNum->setSubscribed(true);

        $this->assertEquals('fluent_company', $this->logisticsNum->getCompany());
        $this->assertEquals('fluent_number', $this->logisticsNum->getNumber());
        $this->assertEquals('fluent_phone', $this->logisticsNum->getPhoneNumber());
        $this->assertEquals('fluent_from', $this->logisticsNum->getFromCity());
        $this->assertEquals('fluent_to', $this->logisticsNum->getToCity());
        $this->assertEquals('fluent_status', $this->logisticsNum->getLatestStatus());
        $this->assertTrue($this->logisticsNum->isSubscribed());
    }

    public function testToString(): void
    {
        // 测试__toString方法
        $this->assertEquals('123456789', $this->logisticsNum->__toString());

        // 测试空number的情况
        $emptyLogisticsNum = new LogisticsNum();
        $this->assertEquals('', $emptyLogisticsNum->__toString());

        // 使用反射设置ID来测试ID作为fallback
        $reflection = new \ReflectionClass($emptyLogisticsNum);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($emptyLogisticsNum, '123456789');

        $this->assertEquals('123456789', $emptyLogisticsNum->__toString());
    }

    public function testSetterEffects(): void
    {
        // 测试setter方法的效果，而不是返回值
        $this->logisticsNum->setCompany('test_company');
        $this->assertEquals('test_company', $this->logisticsNum->getCompany());

        $this->logisticsNum->setNumber('test_number');
        $this->assertEquals('test_number', $this->logisticsNum->getNumber());

        $this->logisticsNum->setPhoneNumber('test_phone');
        $this->assertEquals('test_phone', $this->logisticsNum->getPhoneNumber());

        $this->logisticsNum->setFromCity('test_from');
        $this->assertEquals('test_from', $this->logisticsNum->getFromCity());

        $this->logisticsNum->setToCity('test_to');
        $this->assertEquals('test_to', $this->logisticsNum->getToCity());

        $this->logisticsNum->setLatestStatus('test_status');
        $this->assertEquals('test_status', $this->logisticsNum->getLatestStatus());

        $syncTime = new \DateTimeImmutable();
        $this->logisticsNum->setSyncTime($syncTime);
        $this->assertSame($syncTime, $this->logisticsNum->getSyncTime());

        $this->logisticsNum->setSubscribed(true);
        $this->assertTrue($this->logisticsNum->isSubscribed());

        $this->logisticsNum->setAccount(null);
        $this->assertNull($this->logisticsNum->getAccount());
    }

    public function testFluentCollectionMethods(): void
    {
        // 测试流式返回类型的集合方法
        $status1 = new LogisticsStatus();
        $status2 = new LogisticsStatus();

        $result1 = $this->logisticsNum->addStatusList($status1);
        $result2 = $this->logisticsNum->addStatusList($status2);
        $result3 = $this->logisticsNum->removeStatusList($status1);

        $this->assertSame($this->logisticsNum, $result1);
        $this->assertSame($this->logisticsNum, $result2);
        $this->assertSame($this->logisticsNum, $result3);
    }

    public function testTypeSafety(): void
    {
        // 测试类型安全
        $now = new \DateTimeImmutable();
        $account = new Account();

        // 验证DateTimeImmutable类型
        $this->logisticsNum->setSyncTime($now);
        $this->assertSame($now, $this->logisticsNum->getSyncTime());

        // 验证null值
        $this->logisticsNum->setSyncTime(null);
        $this->assertNull($this->logisticsNum->getSyncTime());

        // 验证Account类型
        $this->logisticsNum->setAccount($account);
        $this->assertSame($account, $this->logisticsNum->getAccount());

        // 验证Account的null值
        $this->logisticsNum->setAccount(null);
        $this->assertNull($this->logisticsNum->getAccount());
    }
}
