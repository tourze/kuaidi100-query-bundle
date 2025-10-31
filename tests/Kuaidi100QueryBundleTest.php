<?php

declare(strict_types=1);

namespace Kuaidi100QueryBundle\Tests;

use Kuaidi100QueryBundle\Kuaidi100QueryBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(Kuaidi100QueryBundle::class)]
#[RunTestsInSeparateProcesses]
final class Kuaidi100QueryBundleTest extends AbstractBundleTestCase
{
}
