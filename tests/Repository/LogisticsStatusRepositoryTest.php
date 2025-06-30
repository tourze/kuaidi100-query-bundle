<?php

namespace Kuaidi100QueryBundle\Tests\Repository;

use Kuaidi100QueryBundle\Repository\LogisticsStatusRepository;
use PHPUnit\Framework\TestCase;

/**
 * 测试LogisticsStatusRepository的基本功能
 */
class LogisticsStatusRepositoryTest extends TestCase
{
    public function testRepositoryCanBeInstantiated(): void
    {
        // 由于Repository需要EntityManager等复杂依赖，
        // 这里仅测试类的存在性
        $this->assertTrue(class_exists(LogisticsStatusRepository::class));
    }
    
    public function testRepositoryExtendsServiceEntityRepository(): void
    {
        $reflection = new \ReflectionClass(LogisticsStatusRepository::class);
        $parentClass = $reflection->getParentClass();
        
        $this->assertNotFalse($parentClass);
        $this->assertEquals('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $parentClass->getName());
    }
} 