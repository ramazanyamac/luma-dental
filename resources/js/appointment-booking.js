export function initAppointmentBooking(root, { getSlot, setStep, refreshSlots }) {
    const copy = JSON.parse(root.querySelector('#booking-copy').textContent);
    const form = root.querySelector('#appointment-details-form');
    const confirm = root.querySelector('[data-confirm-booking]');
    const error = root.querySelector('[data-booking-error]');
    const locale = document.documentElement.lang === 'tr' ? 'tr-TR' : 'en-GB';
    const formatter = new Intl.DateTimeFormat(locale, {
        dateStyle: 'long', timeStyle: 'short', timeZone: root.dataset.timezone,
    });
    let validatedData = null;
    let busy = false;
    let calendarUrl = null;

    function setValues(prefix, values) {
        for (const [key, value] of Object.entries(values)) {
            const element = root.querySelector(`[data-${prefix}-value="${key}"]`);
            if (element) element.textContent = value || '—';
        }
    }

    function review(data) {
        validatedData = new FormData();
        for (const [key, value] of data.entries()) validatedData.append(key, value);
        const treatment = root.querySelector('input[name="treatment_id"]:checked');
        const slot = getSlot();
        if (!treatment || !slot) return;
        validatedData.set('treatment_id', treatment.value);
        validatedData.set('starts_at', slot.starts_at);
        validatedData.set('booking_token', root.dataset.bookingToken);
        const selectedText = (name) => {
            const control = form.querySelector(`[name="${name}"]:checked`);
            return control?.closest('label')?.textContent.trim();
        };
        const birthDate = data.get('birth_date');
        setValues('review', {
            treatment: treatment.dataset.treatmentName,
            datetime: `${formatter.format(new Date(slot.starts_at))} (${root.dataset.timezone})`,
            duration: copy.duration.replace(':minutes', treatment.dataset.duration),
            name: data.get('name'), email: data.get('email'),
            phone: data.get('phone'),
            birth_date: birthDate ? new Intl.DateTimeFormat(locale, { dateStyle: 'long', timeZone: 'UTC' }).format(new Date(`${birthDate}T12:00:00Z`)) : '',
            gender: selectedText('gender'),
            reason: data.get('reason') ? form.querySelector('[name="reason"]').selectedOptions[0].textContent.trim() : '',
            is_new_patient: data.get('is_new_patient') === '1' ? copy.yes : copy.no,
            notes: data.get('notes'),
        });
        error.hidden = true;
        setStep(4);
        root.querySelector('[data-review-title]').focus();
    }

    function calendar(data) {
        const stamp = (value) => new Date(value).toISOString().replace(/[-:]/g, '').replace(/\.\d{3}Z$/, 'Z');
        const escape = (value) => String(value).replace(/\\/g, '\\\\').replace(/\r?\n/g, '\\n').replace(/,/g, '\\,').replace(/;/g, '\\;');
        const lines = ['BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//Luma Dental//Appointments//EN', 'CALSCALE:GREGORIAN', 'BEGIN:VEVENT',
            `UID:${data.reference}@luma-dental`, `DTSTAMP:${stamp(new Date())}`, `DTSTART:${stamp(data.starts_at)}`, `DTEND:${stamp(data.ends_at)}`,
            `SUMMARY:${escape(`Luma Dental — ${data.treatment}`)}`, `LOCATION:${escape(copy.location)}`, 'END:VEVENT', 'END:VCALENDAR'];
        // Fold long lines by UTF-8 bytes, without splitting a character.
        const folded = lines.map((line) => {
            let output = '', bytes = 0;
            for (const char of line) {
                const size = new TextEncoder().encode(char).length;
                if (bytes + size > 75) { output += '\r\n '; bytes = 1; }
                output += char; bytes += size;
            }
            return output;
        }).join('\r\n') + '\r\n';
        if (calendarUrl) URL.revokeObjectURL(calendarUrl);
        calendarUrl = URL.createObjectURL(new Blob([folded], { type: 'text/calendar;charset=utf-8' }));
        const link = root.querySelector('[data-calendar-download]');
        link.href = calendarUrl;
        link.download = `luma-${data.reference}.ics`;
    }

    root.querySelectorAll('[data-review-edit]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!busy) setStep(Number(button.dataset.reviewEdit));
        });
    });

    confirm.addEventListener('click', async () => {
        if (busy || !validatedData) return;
        busy = true;
        confirm.disabled = true;
        confirm.textContent = copy.saving;
        error.hidden = true;
        root.dataset.submitting = 'true';
        root.querySelectorAll('[data-go-to-step], [data-review-edit]').forEach((button) => { button.disabled = true; });
        try {
            const response = await fetch(root.dataset.bookingUrl, {
                method: 'POST', headers: { Accept: 'application/json' },
                credentials: 'same-origin', body: validatedData,
            });
            if (response.status === 419) throw new Error(copy.expired);
            const data = await response.json();
            if (!response.ok) {
                if (data.errors?.starts_at || data.errors?.treatment_id) {
                    setStep(2);
                    refreshSlots();
                    const slotError = root.querySelector('[data-slot-error]');
                    slotError.textContent = (data.errors.starts_at ?? data.errors.treatment_id)[0];
                    slotError.hidden = false;
                    return;
                }
                throw new Error(response.status === 419 || data.errors?.booking_token ? copy.expired : copy.failed);
            }
            setValues('success', {
                ...data,
                datetime: `${formatter.format(new Date(data.starts_at))} (${root.dataset.timezone})`,
                duration: copy.duration.replace(':minutes', data.duration_minutes), location: copy.location,
            });
            root.querySelector('[data-email-title]').textContent = data.mail_status === 'pending' ? copy.emailQueuedTitle : (data.mail_sent ? copy.emailSentTitle : copy.emailFailedTitle);
            root.querySelector('[data-email-status]').textContent = data.mail_status === 'pending' ? copy.emailQueued : (data.mail_sent ? copy.emailSent : copy.emailFailed);
            root.querySelector('[data-manage-appointment]').href = data.manage_url;
            calendar(data);
            setStep(5);
            root.querySelector('[data-success-title]').focus();
        } catch (failure) {
            error.textContent = failure.message === copy.expired ? copy.expired : copy.failed;
            error.hidden = false;
        } finally {
            busy = false;
            delete root.dataset.submitting;
            confirm.disabled = false;
            confirm.textContent = copy.confirm;
            const current = Number(root.dataset.currentStep);
            root.querySelectorAll('[data-review-edit]').forEach((button) => { button.disabled = false; });
            root.querySelectorAll('[data-go-to-step]').forEach((button) => {
                button.disabled = current === 5 || Number(button.dataset.goToStep) > current;
            });
        }
    });
    return { review };
}
