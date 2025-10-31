<?php

namespace Kuaidi100QueryBundle\Exception;

class JsonEncodeException extends Kuaidi100Exception
{
    public function __construct(string $message = 'JSON编码失败', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
