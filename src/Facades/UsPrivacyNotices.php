<?php

namespace JeffersonGoncalves\UsPrivacyNotices\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\UsPrivacyNotices\NoticeManager;

/**
 * @method static array{business: array<string, mixed>, data_categories: list<string>, sensitive: list<string>, sells: bool, targeted_advertising: bool, profiling: bool, third_parties: list<string>, shared_categories: list<string>, activities: list<array<string, mixed>>} export()
 * @method static bool sells()
 * @method static bool targetsAdvertising()
 * @method static bool profiles()
 * @method static bool processesSensitive()
 * @method static bool needsOptOutLink()
 *
 * @see NoticeManager
 */
class UsPrivacyNotices extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NoticeManager::class;
    }
}
