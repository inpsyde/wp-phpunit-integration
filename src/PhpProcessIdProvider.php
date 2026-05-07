<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration;

use Exception;

class PhpProcessIdProvider
{
    /**
     * @throws Exception
     */
    public function currentProcessId(): int
    {
        $processId = getmypid();

        if ($processId === false) {
            throw new Exception('Could not determine the current process ID.');
        }

        return $processId;
    }
}
