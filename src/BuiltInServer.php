<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Process\Process;

class BuiltInServer
{
    private static ?Process $process = null;

    public function __construct(
        private readonly SymfonyProcessFactory $symfonyProcessFactory,
        private readonly LocalDependencyPath $wordPressPath,
    ) {
    }

    public function start(): void
    {
        $this->stop();

        // TODO: Maybe make the port configurable through environment variables or a configuration file.
        // The alphabet positions for S Y D E is 19 25 4 5
        self::$process = $this->symfonyProcessFactory->create([
            'php',
            '-S',
            '0.0.0.0:19254',
            '-t',
            $this->wordPressPath->path(),
        ]);

        self::$process->start();
    }

    public function stop(): void
    {
        if (!self::$process instanceof Process) {
            return;
        }

        self::$process->stop();
    }
}
