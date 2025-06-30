<?php

namespace Kuaidi100QueryBundle\Tests\Unit\Exception;

use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use PHPUnit\Framework\TestCase;

class AccountNotFoundExceptionTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $exception = new AccountNotFoundException();

        $this->assertSame('未找到可用的账户配置', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithMessage(): void
    {
        $message = 'Custom error message';
        $exception = new AccountNotFoundException($message);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithAllParameters(): void
    {
        $message = 'Custom error message';
        $code = 123;
        $previous = new \RuntimeException('Previous exception');

        $exception = new AccountNotFoundException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testIsInstanceOfRuntimeException(): void
    {
        $exception = new AccountNotFoundException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}