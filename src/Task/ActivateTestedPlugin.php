<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Task;

use Syde\WpPhpunitIntegration\Path\LocalDependencyPath;
use Syde\WpPhpunitIntegration\WpCli;
use Symfony\Component\Filesystem\Path;

class ActivateTestedPlugin implements Task
{
    public function __construct(
        private readonly LocalDependencyPath $packageRootPath,
        private readonly WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        $name = Path::getFilenameWithoutExtension($this->packageRootPath->path());

        $this->wpCli->run(['plugin', 'activate', $name]);
    }
}
