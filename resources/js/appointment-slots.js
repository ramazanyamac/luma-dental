export function initAppointmentSlots(root, onSelectionChange) {
    const copy = JSON.parse(root.dataset.slotsCopy);

    const status = root.querySelector('[data-slots-status]');
    const container = root.querySelector('[data-slots-container]');
    const retry = root.querySelector('[data-slots-retry]');
    const summary = root.querySelector('[data-appointment-summary]');
    const summaryTreatment = root.querySelector('[data-summary-treatment]');
    const summaryTime = root.querySelector('[data-summary-time]');

    const groups = {
        morning: {
            section: root.querySelector('[data-slots-group="morning"]'),
            grid: root.querySelector('[data-slots-grid="morning"]'),
        },
        afternoon: {
            section: root.querySelector('[data-slots-group="afternoon"]'),
            grid: root.querySelector('[data-slots-grid="afternoon"]'),
        },
    };

    let controller = null;
    let requestId = 0;
    let selectedSlot = null;
    let lastQuery = null;

    function reset() {
        controller?.abort();
        requestId++;

        selectedSlot = null;
        lastQuery = null;

        retry.hidden = true;
        summary.hidden = true;

        summaryTreatment.textContent = '';
        summaryTime.textContent = '';
        status.textContent = copy.selectDate;

        container.setAttribute('aria-busy', 'false');

        Object.values(groups).forEach((group) => {
            group.section.hidden = true;
            group.grid.replaceChildren();
        });

        onSelectionChange();
    }

    function selectSlot(slot) {
        selectedSlot = slot;

        const treatment = root.querySelector(
            'input[name="treatment_id"]:checked'
        );

        summaryTreatment.textContent =
            treatment?.dataset.treatmentName || '';

        summaryTime.textContent = `${copy.timeLabel}: ${slot.label}`;
        summary.hidden = false;

        onSelectionChange();
    }

    function renderSlots(slots) {
        slots.forEach((slot) => {
            const hour = Number(slot.label.split(':')[0]);
            const group = hour < 12
                ? groups.morning
                : groups.afternoon;

            const label = document.createElement('label');
            const input = document.createElement('input');
            const text = document.createElement('span');

            label.className = 'relative block cursor-pointer';

            input.type = 'radio';
            input.name = 'starts_at';
            input.value = slot.starts_at;
            input.className = 'peer sr-only';

            text.textContent = slot.label;

            text.className =
                'flex h-11 items-center justify-center rounded-lg ' +
                'border border-stone-200 bg-white px-2 text-sm ' +
                'font-medium text-slate-700 transition ' +
                'hover:border-teal-700 ' +
                'peer-checked:border-teal-900 ' +
                'peer-checked:bg-teal-900 peer-checked:text-white ' +
                'peer-focus-visible:outline-2 ' +
                'peer-focus-visible:outline-offset-2 ' +
                'peer-focus-visible:outline-teal-800';

            input.addEventListener('change', () => {
                if (input.checked) {
                    selectSlot(slot);
                }
            });

            label.append(input, text);
            group.grid.append(label);
            group.section.hidden = false;
        });
    }

    async function load(query) {
        reset();

        lastQuery = { ...query };

        const currentRequest = requestId;
        controller = new AbortController();

        status.textContent = copy.loading;
        container.setAttribute('aria-busy', 'true');

        const url = new URL(
            root.dataset.slotsUrl,
            window.location.origin
        );

        url.searchParams.set('date', query.date);
        url.searchParams.set('treatment_id', query.treatmentId);

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
                cache: 'no-store',
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error('Time slots request failed.');
            }

            const data = await response.json();

            if (!Array.isArray(data.slots)) {
                throw new Error('Invalid time slots response.');
            }

            if (currentRequest !== requestId) {
                return;
            }

            renderSlots(data.slots);

            status.textContent = data.slots.length
                ? ''
                : copy.empty;
        } catch (error) {
            if (
                error.name === 'AbortError'
                || currentRequest !== requestId
            ) {
                return;
            }

            status.textContent = copy.failed;
            retry.hidden = false;
        } finally {
            if (currentRequest === requestId) {
                container.setAttribute('aria-busy', 'false');
            }
        }
    }

    retry.addEventListener('click', () => {
        if (lastQuery) {
            load(lastQuery);
        }
    });

    return {
        load,
        reset,
        getSelectedSlot: () => selectedSlot,
    };
}