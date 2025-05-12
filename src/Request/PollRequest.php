<?php

namespace Kuaidi100QueryBundle\Request;

/**
 * 快递订阅物流请求
 *
 * https://api.kuaidi100.com/document/5f0ffa8f2977d50a94e1023c
 */
class PollRequest extends BaseRequest
{
    private string $com;

    private string $num;

    private string $callbackUrl;

    private string $phone;

    public function getRequestPath(): string
    {
        return 'https://poll.kuaidi100.com/poll';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'param' => json_encode([
                'company' => strtolower($this->getCom()), // 订阅的快递公司的编码，一律用小写字母
                'number' => $this->getNum(),
                'from' => '',
                'to' => '',
                'key' => $this->getAccount()->getSignKey(),
                'parameters' => [
                    'resultv2' => 4, // 开启行政区域解析, 0关闭
                    'callbackurl' => $this->getCallbackUrl(),
                    'phone' => $this->getPhone(),
                ],
            ]),
        ];
    }

    public function getCom(): string
    {
        return $this->com;
    }

    public function setCom(string $com): void
    {
        $this->com = $com;
    }

    public function getNum(): string
    {
        return $this->num;
    }

    public function setNum(string $num): void
    {
        $this->num = $num;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $callbackUrl): void
    {
        $this->callbackUrl = $callbackUrl;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }
}
