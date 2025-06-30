<?php

namespace Kuaidi100QueryBundle\Tests\Unit\Enum;

use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use PHPUnit\Framework\TestCase;

class LogisticsStateEnumTest extends TestCase
{
    public function testEnumCases(): void
    {
        $this->assertSame('0', LogisticsStateEnum::ONWAY->value);
        $this->assertSame('1', LogisticsStateEnum::PICKUP->value);
        $this->assertSame('5', LogisticsStateEnum::DELIVER->value);
        $this->assertSame('3', LogisticsStateEnum::SIGN->value);
        $this->assertSame('6', LogisticsStateEnum::RETURN->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('在途', LogisticsStateEnum::ONWAY->getLabel());
        $this->assertSame('揽收', LogisticsStateEnum::PICKUP->getLabel());
        $this->assertSame('派件', LogisticsStateEnum::DELIVER->getLabel());
        $this->assertSame('签收', LogisticsStateEnum::SIGN->getLabel());
        $this->assertSame('退回', LogisticsStateEnum::RETURN->getLabel());
    }

    public function testFromValue(): void
    {
        $this->assertSame(LogisticsStateEnum::ONWAY, LogisticsStateEnum::from('0'));
        $this->assertSame(LogisticsStateEnum::PICKUP, LogisticsStateEnum::from('1'));
        $this->assertSame(LogisticsStateEnum::DELIVER, LogisticsStateEnum::from('5'));
        $this->assertSame(LogisticsStateEnum::SIGN, LogisticsStateEnum::from('3'));
        $this->assertSame(LogisticsStateEnum::RETURN, LogisticsStateEnum::from('6'));
    }

    public function testTryFromValue(): void
    {
        $this->assertSame(LogisticsStateEnum::ONWAY, LogisticsStateEnum::tryFrom('0'));
        $this->assertSame(LogisticsStateEnum::PICKUP, LogisticsStateEnum::tryFrom('1'));
        $this->assertNull(LogisticsStateEnum::tryFrom('invalid'));
    }

    public function testGetCases(): void
    {
        $cases = LogisticsStateEnum::cases();
        $this->assertCount(5, $cases);
        $this->assertContains(LogisticsStateEnum::ONWAY, $cases);
        $this->assertContains(LogisticsStateEnum::PICKUP, $cases);
        $this->assertContains(LogisticsStateEnum::DELIVER, $cases);
        $this->assertContains(LogisticsStateEnum::SIGN, $cases);
        $this->assertContains(LogisticsStateEnum::RETURN, $cases);
    }
}