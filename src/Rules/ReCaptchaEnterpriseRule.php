<?php

namespace Components\GoogleReCaptchaEnterprise\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class ReCaptchaEnterpriseRule implements Rule
{

    protected array $config;

    /**
     * Optional expected action for token (reCAPTCHA action name)
     */
    protected ?string $action = null;

    /**
     * Constructor accepts either:
     * - a string action: new ReCaptchaEnterpriseRule('action')
     * - or an array config (and optional action): new ReCaptchaEnterpriseRule(['project_id' => '...'], 'action')
     *
     * @param array|string $configOrAction
     * @param string|null $action
     */
    public function __construct(array|string $configOrAction = [], ?string $action = null)
    {
        if (is_string($configOrAction) && $action === null) {
            // called as new ReCaptchaEnterpriseRule('action')
            $this->config = [];
            $this->action = $configOrAction;
        } else {
            $this->config = is_array($configOrAction) ? $configOrAction : [];
            $this->action = $action ?? ($this->config['action'] ?? null);
        }
    }

    public function passes($attribute, $value)
    {
        // Empty token -> fail
        if (empty($value)) {
            return false;
        }

        // If the package consumer provided a custom callback, defer to it
        if (isset($this->config['validator_callback']) && is_callable($this->config['validator_callback'])) {
            return (bool) call_user_func($this->config['validator_callback'], $value);
        }

        // Resolve configuration (prefer explicit config passed to rule, then package config, then env)
        $project = $this->config['project_id'] ?? config('recaptcha-enterprise.project_id') ?? env('RECAPTCHA_ENTERPRISE_PROJECT_ID');
        $siteKey = $this->config['site_key'] ?? config('recaptcha-enterprise.site_key') ?? env('RECAPTCHA_ENTERPRISE_SITE_KEY');
        $secret = $this->config['secret'] ?? config('recaptcha-enterprise.secret') ?? env('RECAPTCHA_ENTERPRISE_SECRET');
        $threshold = $this->config['threshold'] ?? config('recaptcha-enterprise.threshold', 0.6);

        // Basic guard: project and secret are required for the Enterprise API call
        if (empty($project) || empty($secret)) {
            return false;
        }

        $expectedAction = $this->action ?? ($this->config['expected_action'] ?? null);

        $url = "https://recaptchaenterprise.googleapis.com/v1/projects/{$project}/assessments?key={$secret}";

        try {
            $response = Http::post($url, [
                'event' => [
                    'token' => $value,
                    'expectedAction' => $expectedAction,
                    'siteKey' => $siteKey,
                ],
            ]);
        } catch (\Throwable $e) {
            return false;
        }

        if (!$response->successful()) {
            return false;
        }

        $achievedScore = data_get($response->json(), 'riskAnalysis.score', 0);

        return $achievedScore >= (float) $threshold;
    }

    public function message()
    {
        return trans('recaptcha::recaptcha.invalid');
    }
}
