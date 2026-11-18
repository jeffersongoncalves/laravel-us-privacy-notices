@props(['requestsUrl' => null, 'choicesUrl' => null, 'consentUrl' => null])
@php
    $notices = app(\JeffersonGoncalves\UsPrivacyNotices\NoticeManager::class);
    $data = $notices->export();
    $updatedAt = \JeffersonGoncalves\UsPrivacyNotices\Models\ProcessingActivity::query()->max('updated_at');
    $label = fn (string $group, ?string $key): ?string => $key === null ? null
        : (\Illuminate\Support\Facades\Lang::has("us-privacy-notices::policy.{$group}.{$key}") ? __("us-privacy-notices::policy.{$group}.{$key}") : $key);
    $types = fn (array $keys): string => collect($keys)->map(fn (string $key): string => $label('types', $key))->implode(', ');
@endphp
<article {{ $attributes->merge(['class' => 'us-privacy-notice']) }}>
    <h2>{{ __('us-privacy-notices::policy.title') }}</h2>
    @if ($updatedAt)
        <p>{{ __('us-privacy-notices::policy.updated_at', ['date' => \Illuminate\Support\Carbon::parse($updatedAt)->translatedFormat('m/d/Y')]) }}</p>
    @endif
    @if ($data['business']['name'] ?? null)
        <p>{{ __('us-privacy-notices::policy.intro', ['name' => $data['business']['name']]) }}</p>
    @endif

    <section>
        <h3>{{ __('us-privacy-notices::policy.collected') }}</h3>
        <p>{{ $data['data_categories'] === [] ? __('us-privacy-notices::policy.none') : implode(', ', $data['data_categories']) }}</p>
        @if ($data['sensitive'] !== [])
            <p>{{ __('us-privacy-notices::policy.sensitive', ['types' => $types($data['sensitive'])]) }}</p>
        @endif
    </section>

    <section>
        <h3>{{ __('us-privacy-notices::policy.processing') }}</h3>
        @foreach ($data['activities'] as $activity)
            <section>
                <h4>{{ $activity['name'] }}</h4>
                <dl>
                    <dt>{{ __('us-privacy-notices::policy.purpose') }}</dt>
                    <dd>{{ $activity['purpose'] }}</dd>
                    @if ($activity['data_categories'] !== [])
                        <dt>{{ __('us-privacy-notices::policy.categories_label') }}</dt>
                        <dd>{{ implode(', ', $activity['data_categories']) }}</dd>
                    @endif
                    @if ($activity['sources'] !== [])
                        <dt>{{ __('us-privacy-notices::policy.sources') }}</dt>
                        <dd>{{ implode(', ', $activity['sources']) }}</dd>
                    @endif
                    @if ($activity['recipients'] !== [])
                        <dt>{{ __('us-privacy-notices::policy.recipients') }}</dt>
                        <dd>{{ collect($activity['recipients'])->map(fn (array $r): string => $r['name'].(isset($r['role']) ? ' ('.$label('roles', $r['role']).')' : ''))->implode(', ') }}</dd>
                    @endif
                    @if ($activity['retention'])
                        <dt>{{ __('us-privacy-notices::policy.retention') }}</dt>
                        <dd>{{ $activity['retention'] }}</dd>
                    @endif
                </dl>
            </section>
        @endforeach
    </section>

    <section>
        <h3>{{ __('us-privacy-notices::policy.third_parties') }}</h3>
        @if ($data['third_parties'] === [])
            <p>{{ __('us-privacy-notices::policy.no_third_parties') }}</p>
        @else
            @if ($data['shared_categories'] !== [])<p>{{ __('us-privacy-notices::policy.shared', ['categories' => implode(', ', $data['shared_categories'])]) }}</p>@endif
            <p>{{ __('us-privacy-notices::policy.third_parties_list', ['names' => implode(', ', $data['third_parties'])]) }}</p>
        @endif
    </section>

    <section>
        <h3>{{ __('us-privacy-notices::policy.sale_ads') }}</h3>
        @if (! $data['sells'] && ! $data['targeted_advertising'] && ! $data['profiling'])
            <p>{{ __('us-privacy-notices::policy.no_sale_ads') }}</p>
        @else
            @if ($data['sells'])<p>{{ __('us-privacy-notices::policy.sells') }}</p>@endif
            @if ($data['targeted_advertising'])<p>{{ __('us-privacy-notices::policy.targeted_advertising') }}</p>@endif
            @if ($data['profiling'])<p>{{ __('us-privacy-notices::policy.profiling') }}</p>@endif
            <p>{{ __('us-privacy-notices::policy.opt_out_how') }}</p>
        @endif
    </section>

    <section>
        <h3>{{ __('us-privacy-notices::policy.rights') }}</h3>
        <ul>
            @foreach ((array) __('us-privacy-notices::policy.rights_list') as $right)
                <li>{{ $right }}</li>
            @endforeach
        </ul>
        <p>{{ __('us-privacy-notices::policy.rights_note') }}</p>
        <p>
            {{ __('us-privacy-notices::policy.how') }}
            @if ($requestsUrl ?? ($data['business']['requests_url'] ?? null))<a href="{{ $requestsUrl ?? $data['business']['requests_url'] }}">{{ __('us-privacy-notices::policy.requests_link') }}</a>@endif
            @if ($data['business']['contact'] ?? null) &middot; {{ $data['business']['contact'] }}@endif
        </p>
        <p>{{ __('us-privacy-notices::policy.appeal') }}</p>
        <p>{{ __('us-privacy-notices::policy.no_discrimination') }}</p>
        <x-us-privacy-notices::privacy-choices :choices-url="$choicesUrl" :consent-url="$consentUrl" />
    </section>
</article>
