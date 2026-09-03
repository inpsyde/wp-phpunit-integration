<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\Path\EnvPath;
use Symfony\Component\Dotenv\Dotenv;

readonly class LoadEnvVariables implements Task
{
    public function __construct(private EnvPath $envPath)
    {
    }

    public function execute(): void
    {
        try {
            $path = $this->envPath->path();
        } catch (\Throwable) {
            return;
        }

        (new Dotenv())->usePutenv()->load($path);
    }
}
