<?php

namespace JeffersonGoncalves\UsPrivacyNotices\Tests;

use JeffersonGoncalves\PestPluginDatabaseMigrations\LoadsPackageMigrations;
use JeffersonGoncalves\UsPrivacy\UsPrivacyServiceProvider;
use JeffersonGoncalves\UsPrivacyNotices\Tests\Fixtures\Customer;
use JeffersonGoncalves\UsPrivacyNotices\UsPrivacyNoticesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use LoadsPackageMigrations;

    protected function getPackageProviders($app): array
    {
        return [
            UsPrivacyServiceProvider::class,
            UsPrivacyNoticesServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('us-privacy.models', [Customer::class]);
        $app['config']->set('us-privacy-notices.business', ['name' => 'Acme Inc.', 'contact' => 'privacy@acme.test', 'requests_url' => null]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadPackageMigrations(UsPrivacyNoticesServiceProvider::class);
    }
}
