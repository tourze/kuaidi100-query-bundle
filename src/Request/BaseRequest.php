<?php

namespace Kuaidi100QueryBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use Kuaidi100QueryBundle\Entity\Account;

abstract class BaseRequest extends ApiRequest
{
    private Account $account;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }
}
