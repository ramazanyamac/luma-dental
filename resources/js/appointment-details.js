import {
    createPhoneFormData,
    initPhoneInputs,
} from './phone-input';

export function initAppointmentDetails(root, onValidated) {
    const form = root.querySelector('#appointment-details-form');
    const status = form.querySelector('[data-details-status]');
    const submitLabel = form.querySelector('[data-details-submit-label]');
    const originalLabel = submitLabel.textContent.trim();

    const fields = new Map();
    let submitting = false;

    form.querySelectorAll('[data-field]').forEach((wrapper) => {
        const name = wrapper.dataset.field;

        const controls = wrapper.querySelectorAll(
            'input, select, textarea'
        );

        const error = document.createElement('p');

        error.id = `appointment-${name}-error`;
        error.className = 'mt-2 text-sm text-red-600';
        error.hidden = true;

        wrapper.append(error);

        controls.forEach((control) => {
            const describedBy = control.getAttribute('aria-describedby');

            control.setAttribute(
                'aria-describedby',
                [describedBy, error.id].filter(Boolean).join(' ')
            );
        });

        fields.set(name, { controls, error });
    });

    function clearErrors() {
        fields.forEach(({ controls, error }) => {
            error.hidden = true;
            error.textContent = '';

            controls.forEach((control) => {
                control.removeAttribute('aria-invalid');
                control.classList.remove('border-red-500');

                if (!['radio', 'checkbox'].includes(control.type)) {
                    control.classList.add('border-stone-200');
                }
            });
        });

        status.textContent = '';
        status.classList.remove('text-red-600', 'text-teal-800');
    }

    function showErrors(errors) {
        let firstInvalid = null;

        fields.forEach(({ controls, error }, name) => {
            const messages = name === 'phone'
                ? errors.phone ?? errors.phone_country
                : errors[name];

            if (!messages?.length) {
                return;
            }

            error.textContent = messages[0];
            error.hidden = false;

            controls.forEach((control) => {
                control.setAttribute('aria-invalid', 'true');

                if (!['radio', 'checkbox'].includes(control.type)) {
                    control.classList.remove('border-stone-200');
                    control.classList.add('border-red-500');
                }
            });

            firstInvalid ??= controls[0];
        });

        firstInvalid?.focus();
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (submitting) {
            return;
        }

        submitting = true;
        clearErrors();

        form.setAttribute('aria-busy', 'true');
        submitLabel.textContent = form.dataset.validating;

        const formData = createPhoneFormData(form);
        let validated = false;
        const controls = [...form.querySelectorAll('input, select, textarea, button'), ...root.querySelectorAll('[data-go-to-step]')];
        const disabledStates = controls.map((control) => control.disabled);
        controls.forEach((control) => { control.disabled = true; });

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
                body: formData,
            });

            const data = await response.json();

            if (response.status === 422) {
                showErrors(data.errors ?? {});

                status.textContent = form.dataset.invalid;
                status.classList.add('text-red-600');

                return;
            }

            if (!response.ok) {
                throw new Error('Details validation failed.');
            }

            formData.set('phone', data.phone);
            validated = true;
        } catch {
            status.textContent = form.dataset.failed;
            status.classList.add('text-red-600');
        } finally {
            submitting = false;
            form.removeAttribute('aria-busy');
            submitLabel.textContent = originalLabel;
            controls.forEach((control, index) => { control.disabled = disabledStates[index]; });
            if (validated) onValidated(formData);
        }
    });

    return {
        activate() {
            initPhoneInputs(form);

            root.querySelector('[data-details-title]').focus();
        },
    };
}