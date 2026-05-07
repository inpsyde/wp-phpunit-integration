<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Sets up the environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    /**
     * Tears down the environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
