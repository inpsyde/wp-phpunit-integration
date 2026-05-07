<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration;

use Syde\WpPhpunitIntegration\Path\LocalDependencyPath;
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

        self::$process = $this->symfonyProcessFactory->create([
            'php',
            '-S',
            '0.0.0.0:8889',
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
