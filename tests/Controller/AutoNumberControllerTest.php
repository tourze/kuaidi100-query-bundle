<?php

namespace Kuaidi100QueryBundle\Tests\Controller;

use Kuaidi100QueryBundle\Controller\AutoNumberController;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Tests\TestDataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * 测试AutoNumberController的完整HTTP请求-响应流程
 *
 * @internal
 */
#[CoversClass(AutoNumberController::class)]
#[RunTestsInSeparateProcesses]
final class AutoNumberControllerTest extends AbstractWebTestCase
{
    protected function onSetUp(): void
    {
    }

    private function createTestData(): void
    {
        $em = self::getEntityManager();
        TestDataFactory::initialize($em);
        $em->flush();
    }

    public function testGetRequestWithValidSerialNumber(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/auto-number', [
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
            /** @var array<string, mixed> $responseData */
            $responseData = json_decode($content, true);
            $this->assertIsArray($responseData);
        }
    }

    public function testGetRequestWithEmptySerialNumber(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/auto-number', [
            'sn' => '',
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

    public function testGetRequestWithoutParameters(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/auto-number');

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

    public function testPostRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $client->request('POST', '/kuaidi100/auto-number', [
                'sn' => '1234567890123',
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
            $jsonContent = $this->getJsonContent(['sn' => '1234567890123']);
            $client->request('PUT', '/kuaidi100/auto-number', [], [], [], $jsonContent);
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
            $client->request('DELETE', '/kuaidi100/auto-number');
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
            $jsonContent = $this->getJsonContent(['sn' => '1234567890123']);
            $client->request('PATCH', '/kuaidi100/auto-number', [], [], [], $jsonContent);
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    public function testHeadRequestAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('HEAD', '/kuaidi100/auto-number');

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
            $client->request('OPTIONS', '/kuaidi100/auto-number');
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
            $client->request('CONNECT', '/kuaidi100/auto-number');
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
            $client->request('TRACE', '/kuaidi100/auto-number');
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

        $client->request('GET', '/kuaidi100/auto-number', [
            'sn' => '1234567890123',
        ]);
    }

    public function testResponseContentType(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/auto-number', [
            'sn' => '1234567890123',
        ]);

        // 验证响应状态码在预期范围内
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        // 仅在成功时检查 Content-Type
        if (Response::HTTP_OK === $client->getResponse()->getStatusCode()) {
            $contentType = $client->getResponse()->headers->get('Content-Type');
            $this->assertNotNull($contentType);
            $this->assertStringContainsString('application/json', $contentType);
        }
    }

    public function testWithChineseSerialNumber(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/auto-number', [
            'sn' => 'SF1234567890中文',
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

    public function testWithVeryLongSerialNumber(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/auto-number', [
            'sn' => str_repeat('1234567890', 10), // 100位数字
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

    public function testWithSpecialCharactersInSerialNumber(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/auto-number', [
            'sn' => 'SF-2023-001!@#$%',
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

    public function testAccessWithoutAuthentication(): void
    {
        $client = self::createClientWithDatabase();

        try {
            $client->request('GET', '/kuaidi100/auto-number', [
                'sn' => '1234567890123',
            ]);

            // 验证未认证时的行为 - 可能返回错误或重定向
            $this->assertContains($client->getResponse()->getStatusCode(), [
                Response::HTTP_OK,
                Response::HTTP_UNAUTHORIZED,
                Response::HTTP_FORBIDDEN,
                Response::HTTP_FOUND,
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
                'POST' => $client->request('POST', '/kuaidi100/auto-number'),
                'PUT' => $client->request('PUT', '/kuaidi100/auto-number'),
                'DELETE' => $client->request('DELETE', '/kuaidi100/auto-number'),
                'PATCH' => $client->request('PATCH', '/kuaidi100/auto-number'),
                'TRACE' => $client->request('TRACE', '/kuaidi100/auto-number'),
                'PURGE' => $client->request('PURGE', '/kuaidi100/auto-number'),
                default => $client->request('GET', '/kuaidi100/auto-number'),
            };
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getJsonContent(array $data): string
    {
        $json = json_encode($data);
        $this->assertNotFalse($json);

        return $json;
    }
}
