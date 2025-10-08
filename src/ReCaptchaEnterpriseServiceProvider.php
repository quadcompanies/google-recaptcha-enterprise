<?php

namespace Components\GoogleReCaptchaEnterprise;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;

class ReCaptchaEnterpriseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/recaptcha-enterprise.php' => config_path('recaptcha-enterprise.php'),
        ], 'config');

        // Publish view
        $this->publishes([
            __DIR__ . '/../resources/views/recaptcha-enterprise.blade.php' => resource_path('views/vendor/recaptcha-enterprise/recaptcha-enterprise.blade.php'),
        ], 'views');

        // Publish lang
        $this->publishes([
            __DIR__ . '/../resources/lang/en/recaptcha.php' => resource_path('lang/vendor/recaptcha-enterprise/en/recaptcha.php'),
        ], 'lang');

        // Register validation rule via container binding
        $this->app->bind('validation.recaptcha_enterprise', function ($app) {
            return new Rules\ReCaptchaEnterpriseRule(config('recaptcha-enterprise')); 
        });

        // Optionally register a custom validator extension
        $this->app->resolving('validator', function ($validator, $app) {
            /* @var Validator $validator */
            $validator->extend('recaptcha_enterprise', function ($attribute, $value, $parameters, $validator) use ($app) {
                $rule = new Rules\ReCaptchaEnterpriseRule(config('recaptcha-enterprise'));
                return $rule->passes($attribute, $value);
            }, trans('recaptcha::recaptcha.invalid'));
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/recaptcha-enterprise.php', 'recaptcha-enterprise');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'recaptcha-enterprise');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'recaptcha');
    }
}
