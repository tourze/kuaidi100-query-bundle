<?php

namespace Kuaidi100QueryBundle\Exception;

class AccountNotFoundException extends Kuaidi100Exception
{
    public function __construct(string $message = '未找到可用的账户配置', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
