<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Task;

use Syde\PocWpLiteIntegrationTestHelper\WpCli;

class InstallMultisiteWp implements Task
{
    public function __construct(
        private readonly WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        // The integration test setup, for example, of DAM, first installs WordPress, and then it converts into a multisite.
        // Currently, I don't see any downside of installing as multisite from the start and with one command.
        // This assuming we always want to have a multisite, which I think should be fine.
        // https://github.com/inpsyde/digital-asset-manager/blob/628dfc28f5e202bf51909ec0711d8bf60583d179/tests/src/WpInstallExtension.php#L202-L213

        // In the integration tests I reviewed, I didn't see the title, admin user, etc. configurable.
        // Maybe, if we really need it, we could read these from an env file.
        // The database configuration for the current integration tests is using that.
        // https://github.com/inpsyde/digital-asset-manager/blob/628dfc28f5e202bf51909ec0711d8bf60583d179/tests/src/WpInstallExtension.php#L152-L162
        // https://github.com/inpsyde/digital-asset-manager/blob/628dfc28f5e202bf51909ec0711d8bf60583d179/tests/src/WpInstallExtension.php#L127-L130
        $this->wpCli->run([
            'core',
            'multisite-install',
            '--url=localhost:8889',
            '--title=WordPress Test Environment',
            '--admin_user=admin',
            '--admin_password=password',
            '--admin_email=admin@wordpress.localhost',
            '--skip-email',
        ]);
    }
}
