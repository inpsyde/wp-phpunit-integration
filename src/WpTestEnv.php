<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Syde\WpPhpUnitIntegration\Container\ServiceLocator;
use Syde\WpPhpUnitIntegration\Task\Bundle\Cleanup;
use Syde\WpPhpUnitIntegration\Task\Bundle\Load;
use Syde\WpPhpUnitIntegration\Task\Bundle\Setup;

class WpTestEnv
{
    public static function setup(): void
    {
        ServiceLocator::retrieve(Setup::class)->execute();
    }

    public static function load(): void
    {
        ServiceLocator::retrieve(Load::class)->execute();
    }

    public static function cleanup(): void
    {
        ServiceLocator::retrieve(Cleanup::class)->execute();
    }

    public static function filesystem(): Filesystem
    {
        return ServiceLocator::retrieve(Filesystem::class);
    }

    /**
     * @param string[] $args
     */
    public static function runWpCliCommand(array $args): void
    {
        ServiceLocator::retrieve(WpCli::class)->run($args);
    }

    public static function addEarlyAction(
        string $hook,
        callable $callback,
        int $priority = 10,
        int $acceptedArgs = 1,
    ): void {
        \WeCodeMore\earlyAddAction($hook, $callback, $priority, $acceptedArgs);
    }

    public static function addEarlyFilter(
        string $hook,
        callable $callback,
        int $priority = 10,
        int $acceptedArgs = 1,
    ): void {
        \WeCodeMore\earlyAddFilter($hook, $callback, $priority, $acceptedArgs);
    }
}
