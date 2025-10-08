# Google reCAPTCHA Enterprise (Laravel) — Package Template

This repository is a minimal Laravel package template that provides:

- A validation Rule: `Components\\GoogleReCaptchaEnterprise\\Rules\\ReCaptchaEnterpriseRule`
- A Blade drop-in view: `resources/views/recaptcha-enterprise.blade.php` (published to `views/vendor/recaptcha-enterprise`)
- A service provider: `ReCaptchaEnterpriseServiceProvider` which registers the rule and publishes config/views/lang

Installation (local development):

1. Require this package via composer (path repository or VCS) or include it directly in `composer.json` of your app.
2. Publish configuration, views and language files:

   php artisan vendor:publish --provider="Components\\GoogleReCaptchaEnterprise\\ReCaptchaEnterpriseServiceProvider" --tag="config"
   php artisan vendor:publish --provider="Components\\GoogleReCaptchaEnterprise\\ReCaptchaEnterpriseServiceProvider" --tag="views"
   php artisan vendor:publish --provider="Components\\GoogleReCaptchaEnterprise\\ReCaptchaEnterpriseServiceProvider" --tag="lang"

Usage:

1. To use the validation rule in a FormRequest or validator, import and instantiate it:

   use Components\\GoogleReCaptchaEnterprise\\Rules\\ReCaptchaEnterpriseRule;

   // Example: pass the expected action string (must match your JS action)
   $request->validate([
      'g-recaptcha-response' => [new ReCaptchaEnterpriseRule('submit_form')],
   ]);

   // Or pass a config array and an action:
   $request->validate([
      'g-recaptcha-response' => [new ReCaptchaEnterpriseRule(['project_id' => 'my-project-id'], 'submit_form')],
   ]);

2. Or use the validator extension rule name `recaptcha_enterprise` after publishing the package.

3. To drop the Blade snippet into a form, include the view. Pass the action string so the JS stub uses the same action name you validate server-side:

   <!-- Hidden input to be populated with token -->
   <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" value="">

   @include('recaptcha-enterprise::recaptcha-enterprise', ['action' => 'submit_form'])

   // If you publish the view and want a custom input id, pass `inputId` as well:
   @include('recaptcha-enterprise::recaptcha-enterprise', ['action' => 'submit_form', 'inputId' => 'recaptcha_token'])