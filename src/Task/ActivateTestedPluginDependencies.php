<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Syde\WpPhpUnitIntegration\WpCli;
use Symfony\Component\Filesystem\Path;

readonly class ActivateTestedPluginDependencies implements Task
{
    public function __construct(
        private LocalDependencyPath $packageRootPath,
        private WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        $name = Path::getFilenameWithoutExtension($this->packageRootPath->path());

        $requiredPluginsField = json_decode(
            $this->wpCli->run([
                'plugin',
                'get',
                $name,
                '--field=requires_plugins',
                '--format=json',
            ]),
            true,
            JSON_THROW_ON_ERROR,
        );

        if ($requiredPluginsField === '') {
            return;
        }

        $requiredPlugins = array_map(
            'trim',
            explode(',', $requiredPluginsField),
        );

        foreach ($requiredPlugins as $requiredPlugin) {
            $this->wpCli->run([
                'plugin',
                'activate',
                $requiredPlugin,
            ]);
        }
    }
}
