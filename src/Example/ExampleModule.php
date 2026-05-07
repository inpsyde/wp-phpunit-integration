<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Example;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Psr\Container\ContainerInterface;

class ExampleModule implements ExecutableModule
{
    use ModuleClassNameIdTrait;

    public function run(ContainerInterface $container): bool
    {
        // TODO: Implement run() method.
        return true;
    }
}
