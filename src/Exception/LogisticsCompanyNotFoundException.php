<?php

namespace Kuaidi100QueryBundle\Exception;

class LogisticsCompanyNotFoundException extends Kuaidi100Exception
{
    public function __construct(string $message = '找不到指定的物流公司', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
