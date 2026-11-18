<?php

namespace JeffersonGoncalves\UsPrivacyNotices;

use JeffersonGoncalves\UsPrivacyNotices\Commands\NoticesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UsPrivacyNoticesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('us-privacy-notices')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations(['create_us_privacy_processing_activities_table'])
            ->hasCommand(NoticesCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(NoticeManager::class);
    }
}
