<?php

namespace Kuaidi100QueryBundle\Tests\Entity;

use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 测试KuaidiCompany实体的基本功能
 *
 * @internal
 */
#[CoversClass(KuaidiCompany::class)]
final class KuaidiCompanyTest extends AbstractEntityTestCase
{
    private KuaidiCompany $company;

    protected function createEntity(): object
    {
        $company = new KuaidiCompany();
        $company->setName('圆通速递');
        $company->setCode('yuantong');

        return $company;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $now = new \DateTimeImmutable();

        return [
            'name' => ['name', '圆通速递'],
            'code' => ['code', 'yuantong'],
            'remark' => ['remark', '测试备注'],
            'createTime' => ['createTime', $now],
            'updateTime' => ['updateTime', $now],
        ];
    }

    public function testToString(): void
    {
        // 使用反射设置ID
        $reflection = new \ReflectionClass($this->company);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->company, '123456789');

        $this->assertEquals('圆通速递(yuantong)', $this->company->__toString());
    }

    public function testToStringWithoutId(): void
    {
        // 当没有ID时应返回空字符串
        $this->assertEquals('', $this->company->__toString());
    }

    public function testTimestampFields(): void
    {
        $now = new \DateTimeImmutable();

        $this->company->setCreateTime($now);
        $this->company->setUpdateTime($now);

        $this->assertSame($now, $this->company->getCreateTime());
        $this->assertSame($now, $this->company->getUpdateTime());
    }

    public function testRemarkField(): void
    {
        $this->assertNull($this->company->getRemark());

        $this->company->setRemark('test remark');
        $this->assertEquals('test remark', $this->company->getRemark());
    }

    public function testNullValues(): void
    {
        $company = new KuaidiCompany();

        $this->assertNull($company->getName());
        $this->assertNull($company->getCode());
        $this->assertNull($company->getId());
        $this->assertNull($company->getCreateTime());
        $this->assertNull($company->getUpdateTime());
        $this->assertNull($company->getRemark());
    }

    public function testToArray(): void
    {
        // 测试toArray方法
        $expected = [
            'code' => 'yuantong',
            'name' => '圆通速递',
        ];

        $this->assertEquals($expected, $this->company->toArray());
    }

    public function testRetrieveApiArray(): void
    {
        // 测试retrieveApiArray方法
        $now = new \DateTimeImmutable('2023-01-01 12:00:00');
        $this->company->setCreateTime($now);
        $this->company->setUpdateTime($now);
        $this->company->setRemark('测试备注');

        // 使用反射设置ID
        $reflection = new \ReflectionClass($this->company);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->company, 1);

        $expected = [
            'id' => 1,
            'createTime' => '2023-01-01 12:00:00',
            'updateTime' => '2023-01-01 12:00:00',
            'name' => '圆通速递',
            'code' => 'yuantong',
            'remark' => '测试备注',
        ];

        $this->assertEquals($expected, $this->company->retrieveApiArray());
    }

    public function testRetrieveAdminArray(): void
    {
        // 测试retrieveAdminArray方法
        $now = new \DateTimeImmutable('2023-01-01 12:00:00');
        $this->company->setCreateTime($now);
        $this->company->setUpdateTime($now);
        $this->company->setRemark('测试备注');

        // 使用反射设置ID
        $reflection = new \ReflectionClass($this->company);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->company, 1);

        $expected = [
            'id' => 1,
            'createTime' => '2023-01-01 12:00:00',
            'updateTime' => '2023-01-01 12:00:00',
            'name' => '圆通速递',
            'code' => 'yuantong',
            'remark' => '测试备注',
        ];

        $this->assertEquals($expected, $this->company->retrieveAdminArray());
    }

    public function testTypeSafety(): void
    {
        // 测试类型安全
        $company = new KuaidiCompany();

        // 验证setCode方法接受字符串参数
        $company->setCode('test_code');
        $this->assertEquals('test_code', $company->getCode());

        // 验证setName方法接受字符串参数
        $company->setName('test_name');
        $this->assertEquals('test_name', $company->getName());

        // 验证setRemark方法接受可空字符串参数
        $company->setRemark('test_remark');
        $this->assertEquals('test_remark', $company->getRemark());

        // 验证setRemark方法接受null参数
        $company->setRemark(null);
        $this->assertNull($company->getRemark());
    }

    protected function setUp(): void
    {
        $this->company = new KuaidiCompany();
        $this->company->setName('圆通速递');
        $this->company->setCode('yuantong');
    }
}
