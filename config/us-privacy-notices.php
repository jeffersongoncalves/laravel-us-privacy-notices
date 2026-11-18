<?php

return [

    'table' => 'us_privacy_processing_activities',

    /*
     * The business, as it appears in the privacy notice: the states require at least one secure
     * and reliable way to submit requests (and to appeal), described in the notice.
     */
    'business' => [
        'name' => env('US_PRIVACY_BUSINESS_NAME', env('APP_NAME')),
        'contact' => env('US_PRIVACY_BUSINESS_CONTACT'),
        'requests_url' => env('US_PRIVACY_REQUESTS_URL'),
    ],

];
