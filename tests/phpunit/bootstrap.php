<?php

declare(strict_types=1);

//phpcs:disable PSR1.Files.SideEffects
//phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_putenv
//phpcs:disable Inpsyde.CodeQuality.NoTopLevelDefine.Found
//phpcs:disable Syde.PHP.DisallowTopLevelDefine.Found

$libraryPath = dirname(__DIR__, 2);
$vendorPath = "{$libraryPath}/vendor";
if (!realpath($vendorPath)) {
    die('Please install via Composer before running tests.');
}

putenv('LIBRARY_PATH=' . $libraryPath);

if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', "{$vendorPath}/autoload.php");
}

defined('ABSPATH') or define('ABSPATH', "{$vendorPath}/wordpress/wordpress/");

require_once "{$vendorPath}/antecedent/patchwork/Patchwork.php";
require_once "{$vendorPath}/autoload.php";

unset($libraryPath, $vendorPath);

//phpcs:enable
