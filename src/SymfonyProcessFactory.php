<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper;

use Symfony\Component\Process\Process;

class SymfonyProcessFactory
{
    /**
     * @param string[] $command
     */
    public function create(array $command): Process
    {
        return new Process($command);
    }
}
