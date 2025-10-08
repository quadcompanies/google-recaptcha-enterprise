@props([
    'siteKey' => config('recaptcha-enterprise.site_key'),
    // allow passing an action string directly: @include(..., ['action' => 'submit'])
    'action' => null,
    // id of hidden input to populate with the token
    'inputId' => 'g-recaptcha-response',
])

@if(empty($siteKey))
    <!-- recaptcha site key not configured -->
    <div class="text-red-600">reCAPTCHA not configured</div>
@else

<script src="https://www.google.com/recaptcha/enterprise.js?render={{ $siteKey }}"></script>
<script>
grecaptcha.enterprise.ready(function() {
    // Safely encode values into JS
    var siteKey = {!! json_encode($siteKey) !!};
    var action = {!! json_encode($action ?? config('recaptcha-enterprise.expected_action') ?? 'submit') !!};
    var inputId = {!! json_encode($inputId) !!};

    grecaptcha.enterprise.execute(siteKey, { action: action }).then(function(token) {
        var el = document.getElementById(inputId);
        if (el) el.value = token;
    });
});
</script>
@endif
