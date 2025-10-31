<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\DataFixtures;

use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'dev')]
#[When(env: 'test')]
class KuaidiCompanyFixtures extends BasicKuaidiCompanyFixtures
{
}
