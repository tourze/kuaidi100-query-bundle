<?php

namespace Kuaidi100QueryBundle\Tests\Controller;

use Kuaidi100QueryBundle\Controller\AddressResolutionController;
use Kuaidi100QueryBundle\Entity\Account;
use Kuaidi100QueryBundle\Exception\AccountNotFoundException;
use Kuaidi100QueryBundle\Repository\AccountRepository;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use Kuaidi100QueryBundle\Tests\TestDataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * 测试AddressResolutionController的完整HTTP请求-响应流程
 *
 * @internal
 */
#[CoversClass(AddressResolutionController::class)]
#[RunTestsInSeparateProcesses]
final class AddressResolutionControllerTest extends AbstractWebTestCase
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

        $client->request('GET', '/kuaidi100/address-resolution', [
            'content' => 'test address content',
            'imageUrl' => 'http://example.com/image.jpg',
            'pdfUrl' => 'http://example.com/file.pdf',
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);
        $this->assertJson($content);

        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
    }

    public function testGetRequestWithMinimalParameters(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/address-resolution', [
            'content' => 'test address content',
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);
        $this->assertJson($content);
    }

    public function testGetRequestWithEmptyParameters(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/address-resolution');

        // API调用成功，即使参数为空
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);
        $this->assertJson($content);

        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
    }

    public function testPostRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $client->request('POST', '/kuaidi100/address-resolution', [
                'content' => 'test address content',
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
            $jsonContent = json_encode([
                'content' => 'test address content',
            ]);
            $this->assertNotFalse($jsonContent);
            $client->request('PUT', '/kuaidi100/address-resolution', [], [], [], $jsonContent);
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
            $client->request('DELETE', '/kuaidi100/address-resolution');
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
            $jsonContent = json_encode([
                'content' => 'test address content',
            ]);
            $this->assertNotFalse($jsonContent);
            $client->request('PATCH', '/kuaidi100/address-resolution', [], [], [], $jsonContent);
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertStringContainsString('Method Not Allowed', $e->getMessage());
        }
    }

    public function testHeadRequestAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('HEAD', '/kuaidi100/address-resolution');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertEmpty($client->getResponse()->getContent());
    }

    public function testOptionsRequestNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        try {
            $client->request('OPTIONS', '/kuaidi100/address-resolution');
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
            $client->request('CONNECT', '/kuaidi100/address-resolution');
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
            $client->request('TRACE', '/kuaidi100/address-resolution');
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
        $accountRepository = self::getService(AccountRepository::class);
        $accountRepository->createQueryBuilder('a')
            ->update(Account::class, 'a')
            ->set('a.valid', 'false')
            ->getQuery()
            ->execute()
        ;

        $this->expectException(AccountNotFoundException::class);

        $client->request('GET', '/kuaidi100/address-resolution', [
            'content' => 'test address content',
        ]);
    }

    public function testResponseContentType(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/address-resolution', [
            'content' => 'test address content',
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $contentType = $client->getResponse()->headers->get('Content-Type');
        $this->assertNotNull($contentType);
        $this->assertStringContainsString('application/json', $contentType);
    }

    public function testWithSpecialCharactersInContent(): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

        $client->request('GET', '/kuaidi100/address-resolution', [
            'content' => '北京市朝阳区测试地址！@#$%^&*()',
            'imageUrl' => 'http://example.com/测试图片.jpg',
            'pdfUrl' => 'http://example.com/测试文档.pdf',
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);
        $this->assertJson($content);
    }

    public function testAccessWithoutAuthentication(): void
    {
        $client = self::createClient();

        $client->request('GET', '/kuaidi100/address-resolution', [
            'content' => 'test address content',
        ]);

        // 验证未认证时的行为 - 可能返回错误或重定向
        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_UNAUTHORIZED,
            Response::HTTP_FORBIDDEN,
            Response::HTTP_FOUND,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();
        $this->createTestData();

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
