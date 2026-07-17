<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\PHPUnit;

use PHPUnit\Event\TestSuite\Started;
use PHPUnit\Event\TestSuite\StartedSubscriber;
use ReflectionClass;
use Syde\WpPhpUnitIntegration\DeferredWordPressLoad;
use Syde\WpPhpUnitIntegration\EnvVar;

final readonly class DeferredWordPressLoadSubscriber implements StartedSubscriber
{
    public function __construct(private EnvVar $envVar)
    {
    }

    public function notify(Started $event): void
    {
        $testSuite = $event->testSuite();

        if (!$testSuite->isForTestClass()) {
            return;
        }

        $attributes = (new ReflectionClass($testSuite->className()))
            ->getAttributes(DeferredWordPressLoad::class);

        $this->envVar->set(
            DeferredWordPressLoad::ENV_VAR,
            $attributes === [] ? '0' : '1',
        );
    }
}
