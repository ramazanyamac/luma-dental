import { createPhoneFormData } from './phone-input';

const form = document.getElementById('contact-form');

if (form) {
    const button = form.querySelector('button[type="submit"]');
    const buttonLabel = button.querySelector('[data-submit-label]');
    const status = form.querySelector('[data-form-status]');
    const defaultLabel = buttonLabel.textContent.trim();

    const fieldNames = ['name', 'email', 'phone', 'subject', 'message'];
    const fields = new Map();

    let isSubmitting = false;

    // Her alanın altına hata mesajı alanı ekle.
    fieldNames.forEach((name) => {
        const input = form.elements.namedItem(name);
        const error = document.createElement('p');

        error.id = `${input.id}-error`;
        error.className = 'mt-1 text-sm text-red-600';
        error.hidden = true;

        // Select'in ikonunu içeren wrapper'ın dışına yerleştir.
        const anchor = input.closest('[data-input-container]')
    ?? (input.tagName === 'SELECT' ? input.parentElement : input);

        anchor.after(error);

        const descriptions = input.getAttribute('aria-describedby');

        input.setAttribute(
            'aria-describedby',
            [descriptions, error.id].filter(Boolean).join(' ')
        );

        fields.set(name, { input, error });
    });

    function clearErrors() {
        fields.forEach(({ input, error }) => {
            error.textContent = '';
            error.hidden = true;

            input.removeAttribute('aria-invalid');
            input.classList.remove('border-red-500');
            input.classList.add('border-stone-200');
        });

        status.textContent = '';
    }

    function showErrors(errors) {
        let firstInvalidInput = null;

        fields.forEach(({ input, error }, name) => {
            const messages = name === 'phone'
            ? (errors.phone ?? errors.phone_country)
            : errors[name];

            if (!Array.isArray(messages) || messages.length === 0) {
                return;
            }

            error.textContent = messages[0];
            error.hidden = false;

            input.setAttribute('aria-invalid', 'true');
            input.classList.remove('border-stone-200');
            input.classList.add('border-red-500');

            firstInvalidInput ??= input;
        });

        firstInvalidInput?.focus();
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (isSubmitting) {
            return;
        }

        isSubmitting = true;
        clearErrors();

        form.setAttribute('aria-busy', 'true');
        buttonLabel.textContent = form.dataset.sending;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                },
                body: createPhoneFormData(form),
                credentials: 'same-origin',
            });

            const data = await response.json();

            if (response.status === 422) {
                showErrors(data.errors ?? {});

                status.className = 'text-sm text-red-600';
                status.textContent = form.dataset.invalid;
                return;
            }

            if (response.status === 429) {
                status.className = 'text-sm text-red-600';
                status.textContent = data.message;
                return;
            }

            if (!response.ok) {
                throw new Error('Form submission failed');
            }

            form.reset();

            status.className = 'text-sm text-teal-800';
            status.textContent = data.message;
        } catch {
            status.className = 'text-sm text-red-600';
            status.textContent = form.dataset.failed;
        } finally {
            isSubmitting = false;
            form.removeAttribute('aria-busy');
            buttonLabel.textContent = defaultLabel;
        }
    });
}