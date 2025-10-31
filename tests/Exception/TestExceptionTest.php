<?php

namespace Kuaidi100QueryBundle\Tests\Exception;

use Kuaidi100QueryBundle\Exception\Kuaidi100Exception;
use Kuaidi100QueryBundle\Exception\TestException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 测试TestException的基本功能
 *
 * @internal
 */
#[CoversClass(TestException::class)]
final class TestExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionInheritance(): void
    {
        $exception = new TestException('Test message');

        $this->assertInstanceOf(Kuaidi100Exception::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionMessage(): void
    {
        $message = 'Test exception message';
        $exception = new TestException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionCode(): void
    {
        $code = 500;
        $exception = new TestException('Test message', $code);

        $this->assertSame($code, $exception->getCode());
    }
}
