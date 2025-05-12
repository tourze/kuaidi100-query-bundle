<?php

namespace Kuaidi100QueryBundle\Request;

use HttpClientBundle\Request\ApiRequest;

/**
 * 快递智能识别单号
 * https://api.kuaidi100.com/document/5f1106542977d50a94e10241
 */
class Kuaidi100AutoNumber extends ApiRequest
{
    private string $num;

    private string $key;

    public function getRequestPath(): string
    {
        return 'https://poll.kuaidi100.com/autonumber/auto';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'num' => $this->getNum(),
            'key' => $this->getKey(),
        ];
    }

    public function getNum(): string
    {
        return $this->num;
    }

    public function setNum(string $num): void
    {
        $this->num = $num;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }
}
