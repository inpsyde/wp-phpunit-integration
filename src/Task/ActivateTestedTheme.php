<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Task;

use Syde\PocWpLiteIntegrationTestHelper\Path\LocalDependencyPath;
use Syde\PocWpLiteIntegrationTestHelper\WpCli;
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
