<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Package;

enum PackageType: string
{
    case Plugin = 'wordpress-plugin';
    case Theme = 'wordpress-theme';
    case Library = 'library';
    case Other = 'other';
}
