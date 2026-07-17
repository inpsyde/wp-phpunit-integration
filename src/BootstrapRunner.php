<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

readonly class BootstrapRunner
{
    public function __construct(
        private BootstrapLifecycle $bootstrapLifecycle,
        private TestRunnerProcessTracker $phpProcess,
        private ShutdownFunctionRegisterer $phpShutdownRegisterer,
    ) {
    }

    public function execute(): void
    {
        // We have to ensure that the setup, such as creating wp-config.php, installing WordPress, etc. runs only once at the beginning.
        // Similarly, the cleanup, such as removing the created files, should run after the test runner has finished.
        // We don’t want to do this every time a new, separate PHP process is started, for example, when --process-isolation is used.
        // The bootstrap file is executed in all processes, both the main process and any spawned ones.
        if ($this->phpProcess->isCurrentProcessChildProcess()) {
            $this->bootstrapLifecycle->prepare();
            $this->bootstrapLifecycle->load();
            return;
        }

        // Just to be safe, we can do a cleanup upfront.
        $this->bootstrapLifecycle->cleanup();
        $this->bootstrapLifecycle->setup();

        $this->phpShutdownRegisterer->register(fn () => $this->bootstrapLifecycle->cleanup());

        // We need to "prepare" and "load" WordPress for each PHP process.
        // This might happen only once if, for example, the --process-isolation is not used.
        $this->bootstrapLifecycle->prepare();
        $this->bootstrapLifecycle->load();
    }
}
