<?php

use PHPUnit\Framework\TestCase;
use Components\GoogleReCaptchaEnterprise\Rules\ReCaptchaEnterpriseRule;

class ReCaptchaRuleTest extends TestCase
{
    public function test_passes_with_non_empty_value()
    {
        $rule = new ReCaptchaEnterpriseRule();
        $this->assertTrue($rule->passes('g-recaptcha-response', 'token'));
    }

    public function test_fails_with_empty_value()
    {
        $rule = new ReCaptchaEnterpriseRule();
        $this->assertFalse($rule->passes('g-recaptcha-response', ''));
    }
}
