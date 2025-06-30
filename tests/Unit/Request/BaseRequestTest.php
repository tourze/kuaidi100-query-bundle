<?php

namespace Kuaidi100QueryBundle\Tests\Unit\Request;

use HttpClientBundle\Request\ApiRequest;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Request\BaseRequest;
use PHPUnit\Framework\TestCase;

class BaseRequestTest extends TestCase
{
    private BaseRequest $request;

    protected function setUp(): void
    {
        $this->request = new class extends BaseRequest {
            public function getRequestPath(): string
            {
                return '/test';
            }

            public function getRequestOptions(): array
            {
                return ['query' => ['test' => 'value']];
            }
        };
    }

    public function testIsInstanceOfApiRequest(): void
    {
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }

    public function testSetAndGetAccount(): void
    {
        $account = $this->createMock(Account::class);

        $this->request->setAccount($account);

        $this->assertSame($account, $this->request->getAccount());
    }

    public function testAccountProperty(): void
    {
        $account1 = $this->createMock(Account::class);
        $account2 = $this->createMock(Account::class);

        $this->request->setAccount($account1);
        $this->assertSame($account1, $this->request->getAccount());

        $this->request->setAccount($account2);
        $this->assertSame($account2, $this->request->getAccount());
    }
}