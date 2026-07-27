
document.querySelectorAll('[data-tenant]').forEach(form => {
    const submitBtn     = form.querySelector('button.btn-gui');
    const disablerInput = form.querySelector('[data-disabler]');
    const errorspot     = form.querySelector('[data-error]');

    if (disablerInput) {
        // 1. Init: Sync button state with disabler input state on load
        if (submitBtn) {
            submitBtn.disabled = !disablerInput.checked;
        }

        // 2. Change listener: Toggle disabled state whenever it changes
        disablerInput.addEventListener('change', function () {
            if (submitBtn) {
                submitBtn.disabled = !this.checked;
            }
        });
    } else {
        // 3. Fallback: If no element has data-disabler, make sure button is enabled
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    }

    // Form Submit Handler
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        // 1. Re-use form element ID directly as the 'sys' schema key
        const sysId = this.id;

        // 2. Extract DOM values with pure JS types (no 'on' strings)
        const formData = {};

        Array.from(this.elements).forEach(input => {
            // Skip buttons, hidden meta, unnamed, or disabled fields
            if (!input.name || input.disabled || ['submit', 'button', 'hidden'].includes(input.type)) return;

            if (input.type === 'checkbox') {
                formData[input.name] = input.checked; // Direct boolean!
            } else if (input.type === 'radio') {
                if (input.checked) formData[input.name] = input.value;
            } else {
                formData[input.name] = input.value.trim();
            }
        });

        const payload = {
            package: "form",
            controller: "submit",
            action: "incomming",

            sys: sysId,
            vid: null, // visitor id
            tenant: this.dataset.tenant || null,
            form: formData
        };

        fetch('/_', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
            if (data.status === 'success') {
                this.classList.replace('state-is-form', 'state-is-swap');
            }else{
                if (errorspot) errorspot.textContent = data.message
            }
        })
        .catch(err => {
            if (errorspot) errorspot.textContent = "We are sorry, a submission error occurred.";
        });
    });

});

