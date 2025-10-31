<?php

namespace Kuaidi100QueryBundle\Tests\DependencyInjection;

use Kuaidi100QueryBundle\DependencyInjection\Kuaidi100QueryExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(Kuaidi100QueryExtension::class)]
final class Kuaidi100QueryExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private Kuaidi100QueryExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new Kuaidi100QueryExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    public function testGetAlias(): void
    {
        $this->assertSame('kuaidi100_query', $this->extension->getAlias());
    }
}
