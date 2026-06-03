<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\WpCli;

readonly class MaybeUpgradeCoreWp implements Task
{
    private const ENV_VAR = 'WP_PHPUNIT_INTEGRATION_WP_CORE_VERSION';

    public function __construct(
        private EnvVar $envVar,
        private WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        $wpCoreVersion = $this->envVar->get(self::ENV_VAR);

        if ($wpCoreVersion === null) {
            return;
        }

        $this->wpCli->run([
            'core',
            'upgrade',
            '--version=' . $wpCoreVersion,
            '--force',
        ]);
    }
}
