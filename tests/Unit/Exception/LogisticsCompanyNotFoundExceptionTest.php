<?php

namespace Kuaidi100QueryBundle\Tests\Unit\Exception;

use Kuaidi100QueryBundle\Exception\LogisticsCompanyNotFoundException;
use PHPUnit\Framework\TestCase;

class LogisticsCompanyNotFoundExceptionTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $exception = new LogisticsCompanyNotFoundException();

        $this->assertSame('找不到指定的物流公司', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithMessage(): void
    {
        $message = 'Custom error message';
        $exception = new LogisticsCompanyNotFoundException($message);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithAllParameters(): void
    {
        $message = 'Custom error message';
        $code = 123;
        $previous = new \RuntimeException('Previous exception');

        $exception = new LogisticsCompanyNotFoundException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testIsInstanceOfRuntimeException(): void
    {
        $exception = new LogisticsCompanyNotFoundException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}