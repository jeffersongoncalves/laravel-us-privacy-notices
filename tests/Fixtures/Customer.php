<?php

namespace JeffersonGoncalves\UsPrivacyNotices\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\UsPrivacy\Attributes\PersonalData;
use JeffersonGoncalves\UsPrivacy\Concerns\InteractsWithPersonalData;
use JeffersonGoncalves\UsPrivacy\Contracts\HasPersonalData;
use JeffersonGoncalves\UsPrivacy\Enums\SensitiveType;

#[PersonalData('email', retention: '5 years', targetedAdvertising: true)]
#[PersonalData('purchases', sold: true)]
#[PersonalData('location', sensitiveType: SensitiveType::PreciseGeolocation)]
class Customer extends Model implements HasPersonalData
{
    use InteractsWithPersonalData;

    protected $table = 'customers';
}
