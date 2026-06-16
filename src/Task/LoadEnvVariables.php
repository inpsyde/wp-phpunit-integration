<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Exception;
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
        } catch (Exception) {
            return;
        }

        (new Dotenv())->usePutenv()->load($path);
    }
}
