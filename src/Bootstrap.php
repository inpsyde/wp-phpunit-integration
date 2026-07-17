<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use PHPUnit\Event\Facade as PHPUnitEventFacade;
use Syde\WpPhpUnitIntegration\Container\ServiceLocator;
use Syde\WpPhpUnitIntegration\Container\SimplestContainer;
use Syde\WpPhpUnitIntegration\PHPUnit\DeferredWordPressLoadSubscriber;

class Bootstrap
{
    public static function init(
        string $packageRootPath,
        BootstrapLifecycle $bootstrapLifecycle = new BootstrapLifecycle(),
    ): void {
        try {
            $container = new SimplestContainer(
                (require realpath(__DIR__ . '/../inc/container.php'))($packageRootPath),
            );
            $testRunnerProcessTracker = $container->get(TestRunnerProcessTracker::class);
            $testRunnerProcessTracker->maybeStoreCurrentProcessAsMainProcess();

            ServiceLocator::init($container);

            if ($testRunnerProcessTracker->isCurrentProcessMainProcess()) {
                PHPUnitEventFacade::instance()->registerSubscriber(
                    new DeferredWordPressLoadSubscriber($container->get(EnvVar::class)),
                );
            }

            (new BootstrapRunner(
                $bootstrapLifecycle,
                $testRunnerProcessTracker,
                $container->get(ShutdownFunctionRegisterer::class),
                $container->get(EnvVar::class),
            ))->execute();
        } catch (\Throwable $exception) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
            fwrite(STDERR, $exception->getMessage() . PHP_EOL);
            exit(1);
        }
    }
}
