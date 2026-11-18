<?php

namespace JeffersonGoncalves\UsPrivacyNotices\Tests;

use JeffersonGoncalves\UsPrivacyNotices\UsPrivacyNoticesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            UsPrivacyNoticesServiceProvider::class,
        ];
    }
}
