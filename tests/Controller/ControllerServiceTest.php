<?php

namespace Kuaidi100QueryBundle\Tests\Controller;

use Kuaidi100QueryBundle\Controller\AddressResolutionController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * 测试控制器服务是否正确注册
 *
 * @internal
 */
#[CoversClass(AddressResolutionController::class)]
#[RunTestsInSeparateProcesses]
final class ControllerServiceTest extends AbstractWebTestCase
{
    public function testControllerServiceExists(): void
    {
        $client = self::createClientWithDatabase();

        // 测试控制器是否可以通过 HTTP 层访问（不创建Account数据，会收到AccountNotFoundException，但说明路由正常工作）
        $client->request('GET', '/kuaidi100/address-resolution');

        // 期望收到某种响应（不一定是成功状态码，但不应该是 404）
        $response = $client->getResponse();
        $this->assertNotEquals(404, $response->getStatusCode(), 'Controller route should be accessible');

        // 测试通过，说明控制器服务正确注册并可以访问
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        try {
            match ($method) {
                'POST' => $client->request('POST', '/kuaidi100/address-resolution'),
                'PUT' => $client->request('PUT', '/kuaidi100/address-resolution'),
                'DELETE' => $client->request('DELETE', '/kuaidi100/address-resolution'),
                'PATCH' => $client->request('PATCH', '/kuaidi100/address-resolution'),
                'TRACE' => $client->request('TRACE', '/kuaidi100/address-resolution'),
                'PURGE' => $client->request('PURGE', '/kuaidi100/address-resolution'),
                default => $client->request('GET', '/kuaidi100/address-resolution'),
            };
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }
}
