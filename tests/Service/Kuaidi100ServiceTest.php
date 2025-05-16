<?php

namespace Kuaidi100QueryBundle\Tests\Service;

use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use Kuaidi100QueryBundle\Request\Kuaidi100QueryRequest;
use Kuaidi100QueryBundle\Request\SignRequest;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * 测试Kuaidi100Service的请求处理逻辑
 */
class Kuaidi100ServiceTest extends TestCase
{
    private Kuaidi100Service $service;
    private \PHPUnit\Framework\MockObject\MockObject $httpClient;
    private \PHPUnit\Framework\MockObject\MockObject $logger;
    private \PHPUnit\Framework\MockObject\MockObject $container;
    
    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);
        
        $this->service = new Kuaidi100Service($this->httpClient);
        
        // 通过反射设置私有属性
        $reflection = new \ReflectionClass($this->service);
        
        $loggerProperty = $reflection->getProperty('apiClientLogger');
        $loggerProperty->setAccessible(true);
        $loggerProperty->setValue($this->service, $this->logger);
        
        // 设置container属性
        $containerProperty = $reflection->getProperty('container');
        $containerProperty->setAccessible(true);
        $containerProperty->setValue($this->service, $this->container);
    }
    
    public function testGetBaseUrl(): void
    {
        // 测试基础URL是否为空
        $this->assertEquals('', $this->service->getBaseUrl());
    }
    
    public function testGetRequestMethod(): void
    {
        // 测试请求方法是否为POST
        $request = $this->createMock(RequestInterface::class);
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getRequestMethod');
        $method->setAccessible(true);
        
        $this->assertEquals('POST', $method->invoke($this->service, $request));
    }
    
    public function testGetRequestOptionsWithNormalRequest(): void
    {
        // 测试普通请求选项生成
        $request = $this->createMock(RequestInterface::class);
        $request->method('getRequestOptions')->willReturn(['test' => 'value']);
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getRequestOptions');
        $method->setAccessible(true);
        
        $options = $method->invoke($this->service, $request);
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);
        $this->assertArrayHasKey('headers', $options);
        
        // 对于普通请求，body中应该只包含test但不包含sign
        $this->assertArrayHasKey('test', $options['body']);
        $this->assertEquals('value', $options['body']['test']);
        // 确认普通请求没有sign字段
        $this->assertArrayNotHasKey('sign', $options['body']);
        
        $this->assertEquals(['Content-Type' => 'application/x-www-form-urlencoded'], $options['headers']);
    }
    
    public function testGetRequestOptionsWithSignRequest(): void
    {
        // 使用更具体的类MockSignRequest来模拟
        $signRequest = new MockSignRequest();
        $signRequest->setRequestOptions(['test' => 'value']);
        $signRequest->setSignStr('test_sign_str');
        $signRequest->setSign('test_sign');
        
        // 期望日志记录签名串
        $this->logger->expects($this->once())
            ->method('info')
            ->with('加密串', ['str' => 'test_sign_str']);
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getRequestOptions');
        $method->setAccessible(true);
        
        $options = $method->invoke($this->service, $signRequest);
        
        // 验证选项
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);
        $this->assertEquals(['test' => 'value', 'sign' => 'test_sign'], $options['body']);
    }
    
    public function testFormatResponseWithSuccessfulResponse(): void
    {
        // 测试成功响应的格式化
        $request = $this->createMock(Kuaidi100QueryRequest::class);
        $response = $this->createMock(ResponseInterface::class);
        
        $successContent = json_encode(['returnCode' => '200', 'data' => ['test' => 'value']]);
        $response->method('getContent')->willReturn($successContent);
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatResponse');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $request, $response);
        
        $this->assertIsArray($result);
        $this->assertEquals('200', $result['returnCode']);
        $this->assertArrayHasKey('data', $result);
    }
    
    public function testFormatResponseWithErrorCode(): void
    {
        // 测试带错误码的响应
        $request = $this->createMock(Kuaidi100QueryRequest::class);
        $response = $this->createMock(ResponseInterface::class);
        
        $errorContent = json_encode(['returnCode' => '500', 'message' => 'Server Error']);
        $response->method('getContent')->willReturn($errorContent);
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatResponse');
        $method->setAccessible(true);
        
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('Server Error');
        
        $method->invoke($this->service, $request, $response);
    }
    
    public function testFormatResponseWithErrorCodeNoMessage(): void
    {
        // 测试带错误码但无错误消息的响应
        $request = $this->createMock(Kuaidi100QueryRequest::class);
        $response = $this->createMock(ResponseInterface::class);
        
        $errorContent = json_encode(['returnCode' => '500']);
        $response->method('getContent')->willReturn($errorContent);
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatResponse');
        $method->setAccessible(true);
        
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('未知错误');
        
        $method->invoke($this->service, $request, $response);
    }
    
    public function testFormatResponseWithEmptyResponse(): void
    {
        // 测试空响应
        $request = $this->createMock(Kuaidi100QueryRequest::class);
        $response = $this->createMock(ResponseInterface::class);
        
        $emptyContent = json_encode([]);
        $response->method('getContent')->willReturn($emptyContent);
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatResponse');
        $method->setAccessible(true);
        
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('请求失败');
        
        $method->invoke($this->service, $request, $response);
    }
    
    public function testRequestIntegration(): void
    {
        // 由于ApiClient需要多个依赖，我们将跳过这个集成测试
        $this->markTestSkipped('因为ApiClient依赖复杂，跳过实际集成测试');
    }
}

/**
 * 模拟SignRequest实现
 */
class MockSignRequest implements RequestInterface, SignRequest
{
    private array $requestOptions = [];
    private string $signStr = '';
    private string $sign = '';
    
    public function getRequestPath(): string
    {
        return 'test_path';
    }
    
    public function getRequestOptions(): ?array
    {
        return $this->requestOptions;
    }
    
    public function setRequestOptions(array $options): void
    {
        $this->requestOptions = $options;
    }
    
    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
    
    public function getSing(): string
    {
        return $this->sign;
    }
    
    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }
    
    public function getSingStr(): string
    {
        return $this->signStr;
    }
    
    public function setSignStr(string $signStr): void
    {
        $this->signStr = $signStr;
    }
} 