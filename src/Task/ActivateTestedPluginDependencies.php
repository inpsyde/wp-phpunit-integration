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
        $testedPluginName = Path::getFilenameWithoutExtension($this->packageRootPath->path());

        foreach ($this->requiredPlugins($testedPluginName) as $requiredPlugin) {
            $this->wpCli->run(['plugin', 'activate', $requiredPlugin]);
        }
    }

    /**
     * @return string[]
     */
    private function requiredPlugins(string $pluginName): array
    {
        $requiredPluginsField = json_decode(
            $this->wpCli->run([
                'plugin',
                'get',
                $pluginName,
                '--field=requires_plugins',
                '--format=json',
            ]),
            flags: JSON_THROW_ON_ERROR,
        );

        if (!is_string($requiredPluginsField) || $requiredPluginsField === '') {
            return [];
        }

        return array_map(
            'trim',
            explode(',', $requiredPluginsField),
        );
    }
}
