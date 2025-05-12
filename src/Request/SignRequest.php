<?php

namespace Kuaidi100QueryBundle\Request;

/**
 * 不同接口加密参数不一样
 */
interface SignRequest
{
    public function getSing(): string;

    public function getSingStr(): string;
}
