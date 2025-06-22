<?php

namespace Kuaidi100QueryBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use HttpClientBundle\Request\CacheRequest;
use Kuaidi100QueryBundle\Entity\Account;

/**
 * 智能地址解析接口
 * https://api.kuaidi100.com/document/dizhijiexi02
 */
class Kuaidi100Resolution extends ApiRequest implements CacheRequest, SignRequest
{
    private string $t;

    private string $content = '';

    private string $imageUrl = '';

    private string $pdfUrl = '';

    private Account $account;

    public function getRequestPath(): string
    {
        return 'https://api.kuaidi100.com/address/resolution';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'param' => json_encode($this->getParam()),
            'key' => $this->getAccount()->getSignKey(),
            't' => $this->getT(),
        ];
    }

    public function getT(): string
    {
        return $this->t;
    }

    public function setT(string $t): void
    {
        $this->t = $t;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getPdfUrl(): string
    {
        return $this->pdfUrl;
    }

    public function setPdfUrl(string $pdfUrl): void
    {
        $this->pdfUrl = $pdfUrl;
    }

    public function getCacheKey(): string
    {
        return 'Kuaidi100Resolution_' . md5(json_encode($this->getRequestOptions()));
    }

    public function getCacheDuration(): int
    {
        return 60 * 30;
    }

    public function getSing(): string
    {
        return strtoupper(md5($this->getSingStr()));
    }

    public function getParam(): array
    {
        $res = [];
        if ($this->getContent() !== '') {
            $res['content'] = $this->getContent();
        }
        if ($this->getImageUrl() !== '') {
            $res['imageUrl'] = $this->getImageUrl();
        }
        if ($this->getPdfUrl() !== '') {
            $res['pdfUrl'] = $this->getPdfUrl();
        }

        return $res;
    }

    public function getSingStr(): string
    {
        return json_encode($this->getParam()) . $this->getT() . $this->getAccount()->getSignKey() . $this->getAccount()->getSecret();
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }
}
