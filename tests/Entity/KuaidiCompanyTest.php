<?php

namespace Kuaidi100QueryBundle\Tests\Entity;

use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use PHPUnit\Framework\TestCase;

/**
 * 测试KuaidiCompany实体的基本功能
 */
class KuaidiCompanyTest extends TestCase
{
    private KuaidiCompany $company;
    
    public function testGettersAndSetters(): void
    {
        $this->assertEquals('圆通速递', $this->company->getName());
        $this->assertEquals('yuantong', $this->company->getCode());

        $this->company->setName('申通快递');
        $this->company->setCode('shentong');

        $this->assertEquals('申通快递', $this->company->getName());
        $this->assertEquals('shentong', $this->company->getCode());
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
        $this->assertSame(0, $company->getId());
        $this->assertNull($company->getCreateTime());
        $this->assertNull($company->getUpdateTime());
        $this->assertNull($company->getRemark());
    }
    
    protected function setUp(): void
    {
        $this->company = new KuaidiCompany();
        $this->company->setName('圆通速递');
        $this->company->setCode('yuantong');
    }
} 