<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests;

use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;

class FunctionalTestCase extends TestCase
{
    protected Filesystem $filesystem;
    protected string $workspace;

    protected function setUp(): void
    {
        parent::setUp();

        $classShortName = (new ReflectionClass($this))->getShortName();

        $this->workspace = sys_get_temp_dir() . '/' . $classShortName . time();
        $this->filesystem = new Filesystem();
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->workspace);

        parent::tearDown();
    }
}
