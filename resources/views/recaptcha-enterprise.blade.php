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
<script>
// Ensure a hidden input for reCAPTCHA token exists when there is exactly one form on the page.
function ensureRecaptchaHiddenInput(inputId) {
    try {
        var forms = document.forms || [];
        if (forms.length !== 1) {
            return; // only attach automatically when there's exactly one form
        }

        var form = forms[0];

        var byId = document.getElementById(inputId);
        var byName = document.querySelector('input[name="g-recaptcha-response"]');

        // If an element exists with the expected id and name, nothing to do
        if (byId && byId.getAttribute('name') === 'g-recaptcha-response') {
            return;
        }

        // If there's an element with the name but missing/incorrect id, ensure it has the configured id
        if (byName && (!byName.id || byName.id !== inputId)) {
            byName.id = inputId;
            // ensure it's inside the single form
            if (byName.form !== form) {
                form.appendChild(byName);
            }
            return;
        }

        // If there's an element with the id but missing name, set the name
        if (byId && byId.getAttribute('name') !== 'g-recaptcha-response') {
            byId.setAttribute('name', 'g-recaptcha-response');
            if (byId.form !== form) {
                form.appendChild(byId);
            }
            return;
        }

        // Otherwise create and append a hidden input
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'g-recaptcha-response';
        input.id = inputId;
        form.appendChild(input);
    } catch (e) {
        // silent fail - not critical
        console && console.warn && console.warn('recaptcha input attach failed', e);
    }
}

// Run immediately (in case DOM is already ready) and also on window load as requested
(function(){
    var inputId = {!! json_encode($inputId) !!};
    ensureRecaptchaHiddenInput(inputId);
    window.addEventListener('load', function() { ensureRecaptchaHiddenInput(inputId); });
})();
</script>
@endif
