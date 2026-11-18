<?php

namespace JeffersonGoncalves\UsPrivacyNotices\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\UsPrivacyNotices\UsPrivacyNotices
 */
class UsPrivacyNotices extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-us-privacy-notices';
    }
}
