<?php

namespace Kuaidi100QueryBundle\Tests\Entity;

use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use PHPUnit\Framework\TestCase;

/**
 * 测试LogisticsStatus实体的基本功能
 */
class LogisticsStatusTest extends TestCase
{
    private LogisticsStatus $status;
    
    public function testGettersAndSetters(): void
    {
        $this->assertEquals('1234567890', $this->status->getSn());
        $this->assertEquals('货物已发出', $this->status->getContext());
        $this->assertEquals('2023-01-01 10:00:00', $this->status->getFtime());
        $this->assertEquals(LogisticsStateEnum::ONWAY, $this->status->getState());
        $this->assertEquals('yuantong', $this->status->getCompanyCode());
        $this->assertEquals('test_flag', $this->status->getFlag());

        $this->status->setSn('0987654321');
        $this->status->setContext('货物已签收');
        $this->status->setFtime('2023-01-02 15:30:00');
        $this->status->setState(LogisticsStateEnum::SIGN);
        $this->status->setCompanyCode('shentong');
        $this->status->setFlag('new_flag');

        $this->assertEquals('0987654321', $this->status->getSn());
        $this->assertEquals('货物已签收', $this->status->getContext());
        $this->assertEquals('2023-01-02 15:30:00', $this->status->getFtime());
        $this->assertEquals(LogisticsStateEnum::SIGN, $this->status->getState());
        $this->assertEquals('shentong', $this->status->getCompanyCode());
        $this->assertEquals('new_flag', $this->status->getFlag());
    }
    
    public function testLogisticsNumberRelation(): void
    {
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