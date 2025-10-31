<?php

namespace Kuaidi100QueryBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class Kuaidi100QueryExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
