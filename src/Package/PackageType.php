<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Package;

enum PackageType: string
{
    case Plugin = 'wordpress-plugin';
    case Theme = 'wordpress-theme';
    case Library = 'library';
    case Other = 'other';
}
