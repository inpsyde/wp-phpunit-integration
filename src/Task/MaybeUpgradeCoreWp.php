<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\WpCli;

readonly class MaybeUpgradeCoreWp implements Task
{
    private const WP_CORE_VERSION_ENV_VAR = 'WP_CORE_VERSION';
    private const PREFIX_ENV_VAR = 'WP_PHPUNIT_INTEGRATION_';

    public function __construct(
        private EnvVar $envVar,
        private WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        $wpVersion = $this->envVar->get(self::PREFIX_ENV_VAR . self::WP_CORE_VERSION_ENV_VAR) ?? $this->envVar->get(self::WP_CORE_VERSION_ENV_VAR);

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
