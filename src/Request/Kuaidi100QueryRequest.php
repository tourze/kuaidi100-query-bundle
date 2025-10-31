<?php

namespace Kuaidi100QueryBundle\Request;

use HttpClientBundle\Request\CacheRequest;
use Kuaidi100QueryBundle\Exception\JsonEncodeException;

/**
 * 实时快递查询
 * 每一单查询频率至少大于半小时，否则会造成锁单
 *
 * @see https://api.kuaidi100.com/document/5f0ffb5ebc8da837cbd8aefc
 */
class Kuaidi100QueryRequest extends BaseRequest implements CacheRequest, SignRequest
{
    private string $com;

    private string $num;

    private string $phone;

    public function getRequestPath(): string
    {
        return 'https://poll.kuaidi100.com/poll/query.do';
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequestOptions(): ?array
    {
        return [
            'customer' => $this->getAccount()->getCustomer(),
            'param' => $this->getParam(),
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

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getCacheKey(): string
    {
        return 'KUAIDI100_QUERY_' . $this->getNum();
    }

    public function getCacheDuration(): int
    {
        return 60 * 60;
    }

    public function getSing(): string
    {
        return strtoupper(md5($this->getSingStr()));
    }

    public function getSingStr(): string
    {
        return $this->getParam() . $this->getAccount()->getSignKey() . $this->getAccount()->getCustomer();
    }

    public function getParam(): string
    {
        $result = json_encode([
            'com' => strtolower($this->getCom()),
            'num' => $this->getNum(),
            'phone' => $this->getPhone(),
            'resultv2' => '1',            // 开启行政区域解析
        ], JSON_UNESCAPED_UNICODE);

        if (false === $result) {
            throw new JsonEncodeException('快递查询参数JSON编码失败');
        }

        return $result;
    }
}
