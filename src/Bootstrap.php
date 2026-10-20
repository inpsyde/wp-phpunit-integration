<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Syde\WpPhpUnitIntegration\Container\ServiceLocator;
use Syde\WpPhpUnitIntegration\Container\SimplestContainer;
use Throwable;

class Bootstrap
{
    public static function init(
        string $packageRootPath,
        BootstrapLifecycle $bootstrapLifecycle = new BootstrapLifecycle(),
    ): void {
        try {
            $simplestContainer = new SimplestContainer(
                (require realpath(__DIR__ . '/../inc/container.php'))($packageRootPath),
            );
            $simplestContainer->get(TestRunnerProcessTracker::class)->maybeStoreCurrentProcessAsMainProcess();

            ServiceLocator::init($simplestContainer);

            (new BootstrapRunner(
                $bootstrapLifecycle,
                $simplestContainer->get(TestRunnerProcessTracker::class),
                $simplestContainer->get(ShutdownFunctionRegisterer::class),
            ))->execute();
        } catch (Throwable $throwable) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
            fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
            exit(1);
        }
    }
}
