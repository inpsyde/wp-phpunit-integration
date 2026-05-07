<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration;

use Exception;

class TestRunnerProcessTracker
{
    private const MAIN_PROCESS_ID = 'WP_LITE_INTEGRATION_TEST_HELPER_MAIN_PROCESS_ID';

    public function __construct(
        private readonly PhpProcessIdProvider $phpProcessIdProvider,
        private readonly EnvVar $envVar,
    ) {
    }

    /**
     * @throws Exception
     */
    public function maybeStoreCurrentProcessAsMainProcess(): void
    {
        if ($this->envVar->get(self::MAIN_PROCESS_ID) !== null) {
            return;
        }

        // Setting it as an environment variable makes it possible to access across processes.
        // Globals and static variables do not persist across processes.
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_putenv
        $this->envVar->set(
            self::MAIN_PROCESS_ID,
            (string) $this->phpProcessIdProvider->currentProcessId(),
        );
    }

    /**
     * @throws Exception
     */
    public function isCurrentProcessMainProcess(): bool
    {
        if ($this->envVar->get(self::MAIN_PROCESS_ID) === null) {
            throw new Exception(
                'The main process ID must be stored before checking it.',
            );
        }

        return $this->envVar->get(self::MAIN_PROCESS_ID) === (string) $this->phpProcessIdProvider->currentProcessId();
    }

    /**
     * @throws Exception
     */
    public function isCurrentProcessChildProcess(): bool
    {
        return !$this->isCurrentProcessMainProcess();
    }
}
