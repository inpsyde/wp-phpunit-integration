<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Syde\WpPhpUnitIntegration\WpCli;
use Symfony\Component\Filesystem\Path;

class ActivateTestedTheme implements Task
{
    public function __construct(
        private readonly LocalDependencyPath $packageRootPath,
        private readonly WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        $name = Path::getFilenameWithoutExtension($this->packageRootPath->path());

        $this->wpCli->run(['theme', 'activate', $name]);
    }
}
