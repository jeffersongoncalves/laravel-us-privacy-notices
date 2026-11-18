@props(['choicesUrl' => null, 'consentUrl' => null])
@php($notices = app(\JeffersonGoncalves\UsPrivacyNotices\NoticeManager::class))
@if (($choicesUrl && $notices->needsOptOutLink()) || ($consentUrl && $notices->processesSensitive()))
    <nav {{ $attributes->merge(['class' => 'us-privacy-choices']) }}>
        @if ($choicesUrl && $notices->needsOptOutLink())
            <a href="{{ $choicesUrl }}">{{ __('us-privacy-notices::policy.choices') }}</a>
        @endif
        @if ($consentUrl && $notices->processesSensitive())
            <a href="{{ $consentUrl }}">{{ __('us-privacy-notices::policy.sensitive_consent') }}</a>
        @endif
    </nav>
@endif
