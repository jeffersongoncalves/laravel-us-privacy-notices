<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use JeffersonGoncalves\UsPrivacyNotices\Facades\UsPrivacyNotices;
use JeffersonGoncalves\UsPrivacyNotices\Models\ProcessingActivity;
use JeffersonGoncalves\UsPrivacyNotices\Tests\Fixtures\Customer;

function checkout(): ProcessingActivity
{
    return ProcessingActivity::create([
        'name' => 'Checkout',
        'purpose' => 'Process orders and payments',
        'consumers' => ['customers'],
        'data_categories' => ['contact data', 'purchase history'],
        'models' => [Customer::class],
        'sources' => ['directly from you'],
        'recipients' => [['name' => 'Stripe', 'role' => 'processor'], ['name' => 'AdNetwork', 'role' => 'third_party']],
        'retention' => '5 years after the last order',
    ]);
}

it('exports what the privacy notice discloses', function () {
    checkout();
    ProcessingActivity::create(['name' => 'Support', 'purpose' => 'Answer questions', 'data_categories' => ['messages']]);

    $export = UsPrivacyNotices::export();

    expect($export['business']['name'])->toBe('Acme Inc.')
        ->and($export['data_categories'])->toBe(['contact data', 'messages', 'purchase history'])
        ->and($export['sensitive'])->toBe(['precise_geolocation'])
        ->and($export['sells'])->toBeTrue()
        ->and($export['targeted_advertising'])->toBeTrue()
        ->and($export['profiling'])->toBeFalse()
        ->and($export['third_parties'])->toBe(['AdNetwork'])
        ->and($export['shared_categories'])->toBe(['contact data', 'purchase history'])
        ->and($export['activities'][0]['personal_data'])->toHaveCount(3)
        ->and(UsPrivacyNotices::needsOptOutLink())->toBeTrue()
        ->and(UsPrivacyNotices::processesSensitive())->toBeTrue();
});

it('prints the notice and reminds you of the link and consent', function () {
    checkout();

    Artisan::call('us-privacy:notices');
    expect(Artisan::output())->toContain('Checkout')->toContain('Stripe')->toContain('Your Privacy Choices')->toContain('consent');

    Artisan::call('us-privacy:notices', ['--json' => true]);
    expect(json_decode(Artisan::output(), true)['third_parties'])->toBe(['AdNetwork']);
});

it('renders the privacy notice', function () {
    checkout();

    $html = Blade::render('<x-us-privacy-notices::privacy-policy requests-url="/privacy/requests" choices-url="/choices" consent-url="/consent" />');

    expect($html)->toContain('This notice explains how Acme Inc.')
        ->toContain('contact data, purchase history')
        ->toContain('Sensitive data: precise geolocation.')
        ->toContain('Process orders and payments')
        ->toContain('Stripe (processor)')
        ->toContain('Third parties: AdNetwork.')
        ->toContain('We sell personal data.')
        ->toContain('targeted advertising')
        ->toContain('you can appeal our decision')
        ->toContain('href="/privacy/requests"')
        ->toContain('href="/choices"')
        ->toContain('Your Privacy Choices')
        ->toContain('href="/consent"');

    app()->setLocale('es');

    expect(Blade::render('<x-us-privacy-notices::privacy-policy />'))
        ->toContain('Aviso de privacidad')
        ->toContain('geolocalización precisa');
});

it('hides the links you do not need', function () {
    config()->set('us-privacy.models', []);

    expect(trim(Blade::render('<x-us-privacy-notices::privacy-choices choices-url="/choices" consent-url="/consent" />')))->toBe('');
});
