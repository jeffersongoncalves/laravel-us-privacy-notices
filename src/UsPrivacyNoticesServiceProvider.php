<?php

namespace JeffersonGoncalves\UsPrivacyNotices;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UsPrivacyNoticesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-us-privacy-notices')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
