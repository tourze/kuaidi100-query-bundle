<?php

namespace Kuaidi100QueryBundle\Service;

use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Client\ClientTrait;
use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use Kuaidi100QueryBundle\Request\SignRequest;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Json\Json;

class Kuaidi100Service extends ApiClient
{
    use ClientTrait;

    public function getBaseUrl(): string
    {
        return '';
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return 'POST';
    }

    protected function getRequestOptions(RequestInterface $request): ?array
    {
        $json = $request->getRequestOptions();
        if ($request instanceof SignRequest) {
            $this->apiClientLogger->info('加密串', [
                'str' => $request->getSingStr(),
            ]);
            $json['sign'] = $request->getSing();
        }

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

        $errorCode = ArrayHelper::getValue($json, 'returnCode');
        $message = ArrayHelper::getValue($json, 'message');
        $returnMsg = ArrayHelper::getValue($json, 'message');
        if ($errorCode && '200' !== $errorCode) {
            if ($message) {
                throw new HttpClientException($request, $response, $message);
            }

            throw new HttpClientException($request, $response, $returnMsg ?: '未知错误');
        }

        if (empty($json)) {
            throw new HttpClientException($request, $response, '请求失败');
        }

        return $json;
    }
}
