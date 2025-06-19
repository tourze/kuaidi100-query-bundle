<?php

namespace Kuaidi100QueryBundle\Tests\Entity;

use Kuaidi100QueryBundle\Entity\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    private Account $account;
    
    protected function setUp(): void
    {
        $this->account = new Account();
        $this->account->setCustomer('test_customer');
        $this->account->setUserid('test_userid');
        $this->account->setSecret('test_secret');
        $this->account->setSignKey('test_sign_key');
        $this->account->setValid(true);
    }
    
    public function testGettersAndSetters(): void
    {
        // 测试getter方法返回正确的值
        $this->assertEquals('test_customer', $this->account->getCustomer());
        $this->assertEquals('test_userid', $this->account->getUserid());
        $this->assertEquals('test_secret', $this->account->getSecret());
        $this->assertEquals('test_sign_key', $this->account->getSignKey());
        $this->assertTrue($this->account->isValid());
        
        // 测试修改值后的getter
        $this->account->setCustomer('new_customer');
        $this->account->setUserid('new_userid');
        $this->account->setSecret('new_secret');
        $this->account->setSignKey('new_sign_key');
        $this->account->setValid(false);
        
        $this->assertEquals('new_customer', $this->account->getCustomer());
        $this->assertEquals('new_userid', $this->account->getUserid());
        $this->assertEquals('new_secret', $this->account->getSecret());
        $this->assertEquals('new_sign_key', $this->account->getSignKey());
        $this->assertFalse($this->account->isValid());
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
} 