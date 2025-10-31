<?php

namespace Kuaidi100QueryBundle\Tests\Exception;

use Kuaidi100QueryBundle\Exception\JsonEncodeException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(JsonEncodeException::class)]
final class JsonEncodeExceptionTest extends AbstractExceptionTestCase
{
    public function testDefaultConstructor(): void
    {
        $exception = new JsonEncodeException();

        $this->assertSame('JSON编码失败', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithMessage(): void
    {
        $message = 'Custom JSON encode error';
        $exception = new JsonEncodeException($message);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithAllParameters(): void
    {
        $message = 'Custom JSON encode error';
        $code = 123;
        $previous = new \RuntimeException('Previous exception');

        $exception = new JsonEncodeException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testIsInstanceOfKuaidi100Exception(): void
    {
        $exception = new JsonEncodeException();

        $this->assertNotNull($exception->getMessage());
        $this->assertIsInt($exception->getCode());
    }
}
