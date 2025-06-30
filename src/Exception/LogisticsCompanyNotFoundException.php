<?php

namespace Kuaidi100QueryBundle\Exception;

use RuntimeException;

class LogisticsCompanyNotFoundException extends RuntimeException
{
    public function __construct(string $message = '找不到指定的物流公司', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 