<?php

namespace Kuaidi100QueryBundle\Tests\Enum;

use Kuaidi100QueryBundle\Enum\LogisticsStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(LogisticsStateEnum::class)]
final class LogisticsStateEnumTest extends AbstractEnumTestCase
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

    public function testToArray(): void
    {
        // toArray() 是实例方法，需要在每个枚举实例上调用
        foreach (LogisticsStateEnum::cases() as $case) {
            $array = $case->toArray();

            $this->assertIsArray($array);
            $this->assertArrayHasKey('value', $array);
            $this->assertArrayHasKey('label', $array);
            $this->assertEquals($case->value, $array['value']);
            $this->assertEquals($case->getLabel(), $array['label']);
        }

        // 验证特定枚举的 toArray 结果
        $this->assertEquals(['value' => '0', 'label' => '在途'], LogisticsStateEnum::ONWAY->toArray());
        $this->assertEquals(['value' => '1', 'label' => '揽收'], LogisticsStateEnum::PICKUP->toArray());
        $this->assertEquals(['value' => '5', 'label' => '派件'], LogisticsStateEnum::DELIVER->toArray());
        $this->assertEquals(['value' => '3', 'label' => '签收'], LogisticsStateEnum::SIGN->toArray());
        $this->assertEquals(['value' => '6', 'label' => '退回'], LogisticsStateEnum::RETURN->toArray());
    }
}
