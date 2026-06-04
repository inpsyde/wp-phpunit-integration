<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\WpCli;

readonly class MaybeUpgradeCoreWp implements Task
{
    private const WP_VERSION_ENV_VAR = 'WP_PHPUNIT_INTEGRATION_WP_CORE_UPGRADE_VERSION';

    public function __construct(
        private EnvVar $envVar,
        private WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        $wpVersion = $this->envVar->get(self::WP_VERSION_ENV_VAR);

        if ($wpVersion === null) {
            return;
        }

        $this->wpCli->run([
            'core',
            'upgrade',
            '--version=' . $wpVersion,
            '--force',
        ]);
    }
}
