<?php

namespace Kuaidi100QueryBundle\Tests\Entity;

use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\LogisticsNum;
use Kuaidi100QueryBundle\Entity\LogisticsStatus;
use PHPUnit\Framework\TestCase;

class LogisticsNumTest extends TestCase
{
    private LogisticsNum $logisticsNum;
    
    protected function setUp(): void
    {
        $this->logisticsNum = new LogisticsNum();
        $this->logisticsNum->setCompany('test_company');
        $this->logisticsNum->setNumber('123456789');
        $this->logisticsNum->setPhone('13800138000');
    }
    
    public function testGettersAndSetters(): void
    {
        // 测试基本getter和setter
        $this->assertEquals('test_company', $this->logisticsNum->getCompany());
        $this->assertEquals('123456789', $this->logisticsNum->getNumber());
        $this->assertEquals('13800138000', $this->logisticsNum->getPhone());
        
        // 测试修改值后的getter
        $this->logisticsNum->setCompany('new_company');
        $this->logisticsNum->setNumber('987654321');
        $this->logisticsNum->setPhone('13900139000');
        
        $this->assertEquals('new_company', $this->logisticsNum->getCompany());
        $this->assertEquals('987654321', $this->logisticsNum->getNumber());
        $this->assertEquals('13900139000', $this->logisticsNum->getPhone());
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
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $this->logisticsNum->getStatusList());
        $this->assertCount(0, $this->logisticsNum->getStatusList());
        
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
        $this->assertNull($logisticsNum->getPhone());
        $this->assertNull($logisticsNum->getFromCity());
        $this->assertNull($logisticsNum->getToCity());
        $this->assertNull($logisticsNum->getLatestStatus());
        $this->assertNull($logisticsNum->getSyncTime());
        $this->assertNull($logisticsNum->isSubscribed());
        $this->assertNull($logisticsNum->getAccount());
    }
    
    public function testFluentInterface(): void
    {
        // 测试流式接口
        $result = $this->logisticsNum
            ->setCompany('fluent_company')
            ->setNumber('fluent_number')
            ->setPhone('fluent_phone')
            ->setFromCity('fluent_from')
            ->setToCity('fluent_to')
            ->setLatestStatus('fluent_status')
            ->setSubscribed(true);
        
        $this->assertSame($this->logisticsNum, $result);
        $this->assertEquals('fluent_company', $this->logisticsNum->getCompany());
        $this->assertEquals('fluent_number', $this->logisticsNum->getNumber());
        $this->assertEquals('fluent_phone', $this->logisticsNum->getPhone());
        $this->assertEquals('fluent_from', $this->logisticsNum->getFromCity());
        $this->assertEquals('fluent_to', $this->logisticsNum->getToCity());
        $this->assertEquals('fluent_status', $this->logisticsNum->getLatestStatus());
        $this->assertTrue($this->logisticsNum->isSubscribed());
    }
} 