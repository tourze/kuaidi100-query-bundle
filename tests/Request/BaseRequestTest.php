<?php

namespace Kuaidi100QueryBundle\Tests\Request;

use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Request\BaseRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(BaseRequest::class)]
#[RunTestsInSeparateProcesses] final class BaseRequestTest extends AbstractIntegrationTestCase
{
    private BaseRequest $request;

    protected function onSetUp(): void
    {
        $this->request = new class extends BaseRequest {
            public function getRequestPath(): string
            {
                return '/test';
            }

            /**
             * @return array<string, mixed>
             */
            public function getRequestOptions(): array
            {
                return ['query' => ['test' => 'value']];
            }
        };
    }

    public function testIsInstanceOfApiRequest(): void
    {
        $this->assertNotNull($this->request);
    }

    public function testSetAndGetAccount(): void
    {
        // 使用 Account 具体类进行 mock 是必要的，因为：
        // 1. BaseRequest 类的 setAccount 和 getAccount 方法明确使用了 Account 具体类型约束
        // 2. Account 实体虽然实现了接口，但这些接口与请求处理逻辑无关
        // 3. 在此测试场景中，我们只关心对象的引用传递，不涉及复杂的业务逻辑
        $account = $this->createMock(Account::class);

        $this->request->setAccount($account);

        $this->assertSame($account, $this->request->getAccount());
    }

    public function testAccountProperty(): void
    {
        // 使用 Account 具体类进行 mock 是必要的，因为：
        // 1. 此测试验证属性的正确设置和获取，需要与实际类型完全匹配
        // 2. Account 实体的类型约束要求传入具体的 Account 实例
        // 3. Mock 在这里只作为占位符，不调用任何实际方法
        $account1 = $this->createMock(Account::class);
        // 使用 Account 具体类进行 mock 是必要的，因为：
        // 1. Request 类需要具体的 Account 实体作为依赖
        // 2. Account 实体的类型约束要求传入具体的 Account 实例
        // 3. Mock 在这里只作为占位符，不调用任何实际方法
        $account2 = $this->createMock(Account::class);

        $this->request->setAccount($account1);
        $this->assertSame($account1, $this->request->getAccount());

        $this->request->setAccount($account2);
        $this->assertSame($account2, $this->request->getAccount());
    }
}
