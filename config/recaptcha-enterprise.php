<?php

return [
    // Example config values. Consumers should publish and set these.
    'project_id' => env('RECAPTCHA_ENTERPRISE_PROJECT_ID', ''),
    'site_key' => env('RECAPTCHA_ENTERPRISE_SITE_KEY', ''),
    'secret' => env('RECAPTCHA_ENTERPRISE_SECRET', ''),

    // Optional callback to perform validation (signature: function(string $token): bool)
    'validator_callback' => null,
];
