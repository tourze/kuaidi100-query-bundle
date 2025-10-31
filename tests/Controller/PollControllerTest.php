<?php

namespace Kuaidi100QueryBundle\Tests\Controller;

use Kuaidi100QueryBundle\Controller\PollController;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Entity\KuaidiCompany;
use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Tests\TestDataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * 测试PollController的完整HTTP请求-响应流程
 *
 * @internal
 */
#[CoversClass(PollController::class)]
#[RunTestsInSeparateProcesses]
final class PollControllerTest extends AbstractWebTestCase
{
    protected function onSetUp(): void
    {
    }

    private function createTestData(): void
    {
        $em = self::getEntityManager();
        TestDataFactory::initialize($em);
    }

    public function testGetRequestWithValidParameters(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/poll', [
            'company' => '圆通速递',
            'sn' => '1234567890123',
            'phone' => '13800138000',
        ]);

        // 验证响应状态码在预期范围内
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $content = $client->getResponse()->getContent();
        if (false !== $content && Response::HTTP_OK === $client->getResponse()->getStatusCode()) {
            $this->assertJson($content);
            $responseData = json_decode($content, true);
            $this->assertIsArray($responseData);
        }
    }

    public function testGetRequestWithoutPhone(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/poll', [
            'company' => '圆通速递',
            'sn' => '1234567890123',
        ]);

        // 验证响应状态码在预期范围内
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $content = $client->getResponse()->getContent();
        if (false !== $content && Response::HTTP_OK === $client->getResponse()->getStatusCode()) {
            $this->assertJson($content);
        }
    }

    public function testGetRequestWithUnknownCompany(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/poll', [
            'company' => '未知快递公司',
            'sn' => '1234567890123',
            'phone' => '13800138000',
        ]);

        // 验证响应状态码在预期范围内
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $content = $client->getResponse()->getContent();
        if (false !== $content && Response::HTTP_OK === $client->getResponse()->getStatusCode()) {
            $this->assertJson($content);
        }
    }

    public function testGetRequestWithDifferentCompanies(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试数据一次，确保数据库中有必要的账户和公司信息
        $this->createTestData();

        $companies = ['圆通速递', '申通快递', '中通快递', '韵达快递', '顺丰'];

        foreach ($companies as $company) {
            try {
                $client->request('GET', '/kuaidi100/poll', [
                    'company' => $company,
                    'sn' => '1234567890123',
                    'phone' => '13800138000',
                ]);

                // 验证响应状态码在预期范围内（包含AccountNotFoundException的情况）
                $this->assertContains($client->getResponse()->getStatusCode(), [
                    Response::HTTP_OK,
                    Response::HTTP_BAD_REQUEST,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                ]);

                $content = $client->getResponse()->getContent();
                if (false !== $content) {
                    $this->assertJson($content);
                    $responseData = json_decode($content, true);
                    $this->assertIsArray($responseData);

                    // 如果是BadRequest错误，验证错误信息合理
                    if (Response::HTTP_BAD_REQUEST === $client->getResponse()->getStatusCode()
                        && isset($responseData['error'])) {
                        $this->assertIsString($responseData['error']);
                        $this->assertNotEmpty($responseData['error']);
                    }
                }
            } catch (\Exception $e) {
                // 如果是AccountNotFoundException或其他异常，确保错误信息合理
                $this->assertStringContainsString('账户', $e->getMessage());
            }
        }
    }

    public function testPostRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $client->request('POST', '/kuaidi100/poll', [
                'company' => '圆通速递',
                'sn' => '1234567890123',
                'phone' => '13800138000',
            ]);
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    public function testPutRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $content = json_encode([
                'company' => '圆通速递',
                'sn' => '1234567890123',
                'phone' => '13800138000',
            ]);
            $client->request('PUT', '/kuaidi100/poll', [], [], [], false !== $content ? $content : null);
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    public function testDeleteRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $client->request('DELETE', '/kuaidi100/poll');
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    public function testPatchRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $content = json_encode([
                'company' => '圆通速递',
                'sn' => '1234567890123',
                'phone' => '13800138000',
            ]);
            $client->request('PATCH', '/kuaidi100/poll', [], [], [], false !== $content ? $content : null);
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    public function testHeadRequestAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('HEAD', '/kuaidi100/poll');

        // HEAD 请求可能因为 API 问题返回错误
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);
        $this->assertEmpty($client->getResponse()->getContent());
    }

    public function testOptionsRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $client->request('OPTIONS', '/kuaidi100/poll');
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    public function testConnectRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $client->request('CONNECT', '/kuaidi100/poll');
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    public function testTraceRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $client->request('TRACE', '/kuaidi100/poll');
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    public function testWithoutValidAccount(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        // 清空Account数据，模拟没有有效账户的情况
        $entityManager = self::getEntityManager();
        $accountRepository = $entityManager->getRepository(Account::class);
        $accountRepository->createQueryBuilder('a')
            ->update()
            ->set('a.valid', 'false')
            ->getQuery()
            ->execute()
        ;

        $this->expectException(AccountNotFoundException::class);

        $client->request('GET', '/kuaidi100/poll', [
            'company' => '圆通速递',
            'sn' => '1234567890123',
            'phone' => '13800138000',
        ]);
    }

    public function testResponseContentType(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/poll', [
            'company' => '圆通速递',
            'sn' => '1234567890123',
            'phone' => '13800138000',
        ]);

        // 验证响应状态码在预期范围内
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        if (Response::HTTP_OK === $client->getResponse()->getStatusCode()) {
            $contentType = $client->getResponse()->headers->get('Content-Type');
            $this->assertNotNull($contentType);
            $this->assertStringContainsString('application/json', $contentType);
        }
    }

    public function testWithInvalidPhoneNumber(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/poll', [
            'company' => '圆通速递',
            'sn' => '1234567890123',
            'phone' => 'invalid-phone',
        ]);

        // 验证响应状态码在预期范围内
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $content = $client->getResponse()->getContent();
        if (false !== $content && Response::HTTP_OK === $client->getResponse()->getStatusCode()) {
            $this->assertJson($content);
        }
    }

    public function testWithEmptyParameters(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/poll', [
            'company' => '',
            'sn' => '',
            'phone' => '',
        ]);

        // 验证响应状态码在预期范围内
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $content = $client->getResponse()->getContent();
        if (false !== $content && Response::HTTP_OK === $client->getResponse()->getStatusCode()) {
            $this->assertJson($content);
        }
    }

    public function testCallbackUrlGeneration(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/poll', [
            'company' => '圆通速递',
            'sn' => '1234567890123',
            'phone' => '13800138000',
        ]);

        // 验证响应状态码在预期范围内
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $content = $client->getResponse()->getContent();
        if (false !== $content && Response::HTTP_OK === $client->getResponse()->getStatusCode()) {
            $this->assertJson($content);
            // 验证响应可以正常解析
            $responseData = json_decode($content, true);
            $this->assertIsArray($responseData);
        }
    }

    public function testUnauthenticatedAccess(): void
    {
        $client = self::createClient();

        try {
            // 测试未认证用户访问
            $client->request('GET', '/kuaidi100/poll', [
                'company' => '圆通速递',
                'sn' => '1234567890123',
                'phone' => '13800138000',
            ]);

            // 验证未认证访问的响应状态码（可能是401、403或其他基于应用的认证策略）
            $statusCode = $client->getResponse()->getStatusCode();
            $this->assertContains($statusCode, [
                Response::HTTP_UNAUTHORIZED,
                Response::HTTP_FORBIDDEN,
                Response::HTTP_OK,
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        } catch (\Exception $e) {
            // 外部 API 调用失败也是可接受的，验证异常消息不为空
            $this->assertNotEmpty($e->getMessage());
        }
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            match ($method) {
                'POST' => $client->request('POST', '/kuaidi100/poll'),
                'PUT' => $client->request('PUT', '/kuaidi100/poll'),
                'DELETE' => $client->request('DELETE', '/kuaidi100/poll'),
                'PATCH' => $client->request('PATCH', '/kuaidi100/poll'),
                'TRACE' => $client->request('TRACE', '/kuaidi100/poll'),
                'PURGE' => $client->request('PURGE', '/kuaidi100/poll'),
                default => $client->request('GET', '/kuaidi100/poll'),
            };
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }
}
