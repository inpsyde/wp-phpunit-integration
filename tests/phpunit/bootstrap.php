<?php

declare(strict_types=1);

$packagePath = dirname(__DIR__, 2);
$vendorPath = "{$packagePath}/vendor";

if (!realpath($vendorPath)) {
    die('Please install via Composer before running tests.');
}

require_once "{$vendorPath}/autoload.php";

unset($packagePath, $vendorPath);
