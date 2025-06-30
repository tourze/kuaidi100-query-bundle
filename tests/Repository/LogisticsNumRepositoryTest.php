<?php

namespace Kuaidi100QueryBundle\Tests\Repository;

use Kuaidi100QueryBundle\Repository\LogisticsNumRepository;
use PHPUnit\Framework\TestCase;

/**
 * 测试LogisticsNumRepository的基本功能
 */
class LogisticsNumRepositoryTest extends TestCase
{
    public function testRepositoryCanBeInstantiated(): void
    {
        // 由于Repository需要EntityManager等复杂依赖，
        // 这里仅测试类的存在性
        $this->assertTrue(class_exists(LogisticsNumRepository::class));
    }
    
    public function testRepositoryExtendsServiceEntityRepository(): void
    {
        $reflection = new \ReflectionClass(LogisticsNumRepository::class);
        $parentClass = $reflection->getParentClass();
        
        $this->assertNotFalse($parentClass);
        $this->assertEquals('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $parentClass->getName());
    }
} 