<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Syde\WpPhpUnitIntegration\Container\ServiceLocator;
use Syde\WpPhpUnitIntegration\Container\SimplestContainer;

class Bootstrap
{
    public static function init(
        string $packageRootPath,
        BootstrapLifecycle $bootstrapSequence = new BootstrapLifecycle(),
    ): void {
        try {
            $container = new SimplestContainer(
                (require realpath(__DIR__ . '/../inc/container.php'))($packageRootPath),
            );
            $container->get(TestRunnerProcessTracker::class)->maybeStoreCurrentProcessAsMainProcess();

            ServiceLocator::init($container);

            (new BootstrapRunner(
                $bootstrapSequence,
                $container->get(TestRunnerProcessTracker::class),
                $container->get(ShutdownFunctionRegisterer::class),
            ))->execute();
        } catch (\Throwable $exception) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
            fwrite(STDERR, $exception->getMessage() . PHP_EOL);
            exit(1);
        }
    }
}
