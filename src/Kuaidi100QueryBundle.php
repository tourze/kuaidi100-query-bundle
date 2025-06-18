<?php

namespace Kuaidi100QueryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class Kuaidi100QueryBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \HttpClientBundle\HttpClientBundle::class => ['all' => true],
        ];
    }
}
