<?php

namespace Kuaidi100QueryBundle\Service;

use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Exception\GeneralHttpClientException;
use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Service\SmartHttpClient;
use Kuaidi100QueryBundle\Request\SignRequest;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Json\Json;

#[WithMonologChannel(channel: 'kuaidi100_query')]
class Kuaidi100Service extends ApiClient
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SmartHttpClient $httpClient,
        private readonly LockFactory $lockFactory,
        private readonly CacheInterface $cache,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly AsyncInsertService $asyncInsertService,
    ) {
    }

    protected function getLockFactory(): LockFactory
    {
        return $this->lockFactory;
    }

    protected function getHttpClient(): SmartHttpClient
    {
        return $this->httpClient;
    }

    /**
     * 优先使用Request中定义的地址
     */
    protected function getRequestUrl(RequestInterface $request): string
    {
        $path = ltrim($request->getRequestPath(), '/');
        if (str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'http://')) {
            return $path;
        }

        $domain = trim($this->getBaseUrl());
        if ('' === $domain) {
            throw new \RuntimeException('Kuaidi100Service缺少getBaseUrl的定义');
        }

        return "{$domain}/{$path}";
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getCache(): CacheInterface
    {
        return $this->cache;
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function getAsyncInsertService(): AsyncInsertService
    {
        return $this->asyncInsertService;
    }

    public function getBaseUrl(): string
    {
        return '';
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return 'POST';
    }

    /** @return array{body: array<string, mixed>, headers: array<string, string>} */
    protected function getRequestOptions(RequestInterface $request): array
    {
        $json = $request->getRequestOptions() ?? [];
        if ($request instanceof SignRequest) {
            $this->logger->info('加密串', [
                'str' => $request->getSingStr(),
            ]);
            $json['sign'] = $request->getSing();
        }

        /** @var array<string, mixed> $json */
        return [
            'body' => $json,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        $content = $response->getContent();
        $json = Json::decode($content);

        $this->validateResponseFormat($request, $response, $json);
        if (is_array($json) || is_object($json)) {
            /** @var array<string, mixed>|object $json */
            $this->checkForErrors($request, $response, $json);
        }
        $this->validateNonEmptyResponse($request, $response, $json);

        return $json;
    }

    /**
     * 验证响应格式
     */
    private function validateResponseFormat(
        RequestInterface $request,
        ResponseInterface $response,
        mixed $json
    ): void {
        if (!is_array($json) && !is_object($json)) {
            throw new GeneralHttpClientException($request, $response, '响应格式错误：期望数组或对象类型');
        }
    }

    /**
     * 检查错误信息
     *
     * @param array<string, mixed>|object $json
     */
    private function checkForErrors(
        RequestInterface $request,
        ResponseInterface $response,
        array|object $json
    ): void {
        $errorCode = ArrayHelper::getValue($json, 'returnCode');

        if (null === $errorCode || '' === $errorCode || '200' === $errorCode) {
            return;
        }

        $message = ArrayHelper::getValue($json, 'message');
        if (null !== $message && '' !== $message) {
            $messageStr = $this->convertToString($message);
            throw new GeneralHttpClientException($request, $response, $messageStr);
        }

        $returnMsg = ArrayHelper::getValue($json, 'message');
        $returnMsgStr = $this->convertToString($returnMsg ?? '未知错误');
        throw new GeneralHttpClientException($request, $response, $returnMsgStr);
    }

    /**
     * 验证响应非空
     */
    private function validateNonEmptyResponse(
        RequestInterface $request,
        ResponseInterface $response,
        mixed $json
    ): void {
        if (!is_array($json) || [] === $json) {
            throw new GeneralHttpClientException($request, $response, '请求失败');
        }
    }

    /**
     * 将值转换为字符串
     */
    private function convertToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return (string)$value;
        }

        return '未知错误';
    }
}
