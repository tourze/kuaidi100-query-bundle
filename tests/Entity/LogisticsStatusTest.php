<?php

namespace Kuaidi100QueryBundle\Tests\Entity;

use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 测试LogisticsStatus实体的基本功能
 *
 * @internal
 */
#[CoversClass(LogisticsStatus::class)]
final class LogisticsStatusTest extends AbstractEntityTestCase
{
    private LogisticsStatus $status;

    protected function createEntity(): object
    {
        $status = new LogisticsStatus();
        $status->setSn('1234567890');
        $status->setContext('货物已发出');
        $status->setFtime('2023-01-01 10:00:00');
        $status->setState(LogisticsStateEnum::ONWAY);
        $status->setCompanyCode('yuantong');
        $status->setFlag('test_flag');

        return $status;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $now = new \DateTimeImmutable();

        return [
            'sn' => ['sn', '1234567890'],
            'context' => ['context', '货物已发出'],
            'ftime' => ['ftime', '2023-01-01 10:00:00'],
            'state' => ['state', LogisticsStateEnum::ONWAY],
            'companyCode' => ['companyCode', 'yuantong'],
            'areaCenter' => ['areaCenter', '广州转运中心'],
            'flag' => ['flag', 'test_flag'],
            'createTime' => ['createTime', $now],
            'updateTime' => ['updateTime', $now],
        ];
    }

    public function testLogisticsNumberRelation(): void
    {
        // 使用 LogisticsNum 具体类进行 mock 是必要的，因为：
        // 1. 实体关系测试需要验证具体的实体类型约束
        // 2. LogisticsNum 是数据模型实体，不存在对应的接口
        // 3. 在 ORM 映射中，实体间的关系必须使用具体的实体类
        $logisticsNum = $this->createMock(LogisticsNum::class);
        $this->status->setNumber($logisticsNum);

        $this->assertSame($logisticsNum, $this->status->getNumber());
    }

    public function testAreaCenter(): void
    {
        $this->assertNull($this->status->getAreaCenter());

        $this->status->setAreaCenter('广州转运中心');
        $this->assertEquals('广州转运中心', $this->status->getAreaCenter());
    }

    public function testLocation(): void
    {
        // 测试location字段
        $this->assertNull($this->status->getLocation());

        $this->status->setLocation('广州天河区');
        $this->assertEquals('广州天河区', $this->status->getLocation());

        // 测试设置null值
        $this->status->setLocation(null);
        $this->assertNull($this->status->getLocation());
    }

    public function testToString(): void
    {
        // 使用反射设置ID
        $reflection = new \ReflectionClass($this->status);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->status, '123456789');

        $this->assertEquals('货物已发出', $this->status->__toString());
    }

    public function testToStringWithoutId(): void
    {
        // 当没有ID时应返回空字符串
        $status = new LogisticsStatus();
        $this->assertEquals('', $status->__toString());
    }

    public function testTimestampFields(): void
    {
        $now = new \DateTimeImmutable();

        $this->status->setCreateTime($now);
        $this->status->setUpdateTime($now);

        $this->assertSame($now, $this->status->getCreateTime());
        $this->assertSame($now, $this->status->getUpdateTime());
    }

    public function testToStringActual(): void
    {
        // 实际__toString返回context或sn或id
        $status = new LogisticsStatus();
        $status->setSn('1234567890');
        $status->setContext('货物已发出');

        $this->assertEquals('货物已发出', $status->__toString());

        $statusWithoutContext = new LogisticsStatus();
        $statusWithoutContext->setSn('1234567890');

        $this->assertEquals('1234567890', $statusWithoutContext->__toString());
    }

    public function testNullValues(): void
    {
        $status = new LogisticsStatus();

        $this->assertNull($status->getSn());
        $this->assertNull($status->getContext());
        $this->assertNull($status->getFtime());
        $this->assertNull($status->getState());
        $this->assertNull($status->getCompanyCode());
        $this->assertNull($status->getAreaCenter());
        $this->assertNull($status->getFlag());
        $this->assertNull($status->getNumber());
        $this->assertNull($status->getId());
        $this->assertNull($status->getCreateTime());
        $this->assertNull($status->getUpdateTime());
        // 移除不存在的方法测试
    }

    protected function setUp(): void
    {
        $this->status = new LogisticsStatus();
        $this->status->setSn('1234567890');
        $this->status->setContext('货物已发出');
        $this->status->setFtime('2023-01-01 10:00:00');
        $this->status->setState(LogisticsStateEnum::ONWAY);
        $this->status->setCompanyCode('yuantong');
        $this->status->setFlag('test_flag');
    }
}
