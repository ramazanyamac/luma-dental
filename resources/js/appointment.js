import { initAppointmentSlots } from "./appointment-slots";
import { initAppointmentDetails } from "./appointment-details";

import { initAppointmentBooking } from "./appointment-booking";

const root = document.querySelector("#appointment-booking");

if (root) {
    const copy = JSON.parse(root.dataset.copy);

    const locale = document.documentElement.lang === "tr" ? "tr-TR" : "en-GB";

    const treatmentStep = root.querySelector('[data-booking-step="treatment"]');

    const calendarStep = root.querySelector('[data-booking-step="calendar"]');

    const treatmentFieldset = root.querySelector(
        "#appointment-treatment-selection",
    );

    const treatmentError = root.querySelector("#appointment-treatment-error");

    const calendarTitle = root.querySelector("[data-calendar-title]");
    const monthLabel = root.querySelector("[data-calendar-month]");
    const weekdays = root.querySelector("[data-calendar-weekdays]");
    const days = root.querySelector("[data-calendar-days]");
    const status = root.querySelector("[data-calendar-status]");
    const retry = root.querySelector("[data-calendar-retry]");
    const previousMonth = root.querySelector("[data-previous-month]");
    const nextMonth = root.querySelector("[data-next-month]");
    const selectedDateLabel = root.querySelector("[data-selected-date]");
    const detailsStep = root.querySelector('[data-booking-step="details"]');
    const slotError = root.querySelector("[data-slot-error]");

    const initialMonth = root.dataset.today.slice(0, 7);

    let month = initialMonth;
    let treatmentId = null;
    let selectedDate = null;
    let availableDates = new Set();
    let loaded = false;
    let controller = null;
    let requestId = 0;

    const monthFormatter = new Intl.DateTimeFormat(locale, {
        month: "long",
        year: "numeric",
        timeZone: "UTC",
    });

    const dateFormatter = new Intl.DateTimeFormat(locale, {
        day: "numeric",
        month: "long",
        year: "numeric",
        timeZone: "UTC",
    });

    const weekdayFormatter = new Intl.DateTimeFormat(locale, {
        weekday: "short",
        timeZone: "UTC",
    });

    const slots = initAppointmentSlots(root, updateStepDescriptions);

    const booking = initAppointmentBooking(root, {
        getSlot: () => slots.getSelectedSlot(),
        setStep,
        refreshSlots: () => slots.load({ date: selectedDate, treatmentId }),
    });
    const details = initAppointmentDetails(root, booking.review);

    function updateStepDescriptions() {
        const selectedTreatment = root.querySelector(
            'input[name="treatment_id"]:checked',
        );

        const treatmentDescription = root.querySelector(
            '[data-step-description="1"]',
        );

        const dateDescription = root.querySelector(
            '[data-step-description="2"]',
        );

        treatmentDescription.textContent =
            selectedTreatment?.dataset.treatmentName ||
            treatmentDescription.dataset.defaultText;

        if (!selectedDate) {
            dateDescription.textContent = dateDescription.dataset.defaultText;

            return;
        }

        const formattedDate = dateFormatter.format(
            new Date(`${selectedDate}T12:00:00Z`),
        );

        const selectedSlot = slots.getSelectedSlot();

        if (selectedSlot) {
            slotError.hidden = true;
        }

        dateDescription.textContent = selectedSlot
            ? `${formattedDate} — ${selectedSlot.label}`
            : formattedDate;
    }

    function setStep(step) {
        root.dataset.currentStep = String(step);
        root.dataset.compact = String(step > 1);

        treatmentStep.hidden = step !== 1;
        calendarStep.hidden = step !== 2;

        detailsStep.hidden = step !== 3;
        root.querySelector('[data-booking-step="review"]').hidden = step !== 4;
        root.querySelector('[data-booking-step="success"]').hidden = step !== 5;
        root.querySelector('[data-step-item]').closest('nav').hidden = step === 5;
        if (step === 2) calendarTitle.focus();

        if (step === 3) {
            details.activate();
        }

        root.querySelectorAll("[data-step-item]").forEach((item) => {
            const number = Number(item.dataset.stepItem);
            const button = item.querySelector("[data-go-to-step]");

            item.dataset.status =
                number < step
                    ? "complete"
                    : number === step
                      ? "current"
                      : "upcoming";

            button.disabled = number > step;

            if (number === step) {
                button.setAttribute("aria-current", "step");
            } else {
                button.removeAttribute("aria-current");
            }
        });

        updateStepDescriptions();
    }

    function getMonthDate() {
        return new Date(`${month}-01T12:00:00Z`);
    }

    function renderWeekdays() {
        weekdays.replaceChildren();

        // January 1, 2024 was a Monday.
        for (let index = 0; index < 7; index++) {
            const date = new Date(Date.UTC(2024, 0, 1 + index, 12));

            const label = document.createElement("span");

            label.textContent = weekdayFormatter.format(date);

            weekdays.append(label);
        }
    }

    function selectDate(value) {
        selectedDate = value;
        root.dataset.selectedDate = value;

        days.querySelectorAll("button[data-date]").forEach((button) => {
            button.setAttribute(
                "aria-pressed",
                String(button.dataset.date === value),
            );
        });

        const formatted = dateFormatter.format(new Date(`${value}T12:00:00Z`));

        selectedDateLabel.textContent = `${copy.selectedDate}: ${formatted}`;

        slots.load({
            date: value,
            treatmentId,
        });
    }

    function renderDays() {
        const firstDay = getMonthDate();
        const year = firstDay.getUTCFullYear();
        const monthIndex = firstDay.getUTCMonth();

        const offset = (firstDay.getUTCDay() + 6) % 7;

        const dayCount = new Date(
            Date.UTC(year, monthIndex + 1, 0),
        ).getUTCDate();

        monthLabel.textContent = monthFormatter.format(firstDay);
        previousMonth.disabled = month <= initialMonth;

        days.replaceChildren();

        for (let index = 0; index < offset; index++) {
            const spacer = document.createElement("span");

            spacer.setAttribute("aria-hidden", "true");

            days.append(spacer);
        }

        for (let day = 1; day <= dayCount; day++) {
            const value = `${month}-${String(day).padStart(2, "0")}`;
            const button = document.createElement("button");

            button.type = "button";
            button.dataset.date = value;
            button.textContent = String(day);
            button.disabled = !availableDates.has(value);

            button.setAttribute(
                "aria-label",
                dateFormatter.format(new Date(`${value}T12:00:00Z`)),
            );

            button.setAttribute("aria-pressed", String(selectedDate === value));

            button.className =
                "flex h-11 items-center justify-center rounded-lg " +
                "text-sm font-medium text-teal-900 transition " +
                "enabled:hover:bg-teal-50 " +
                "disabled:cursor-not-allowed disabled:text-stone-300 " +
                "aria-pressed:bg-teal-900 aria-pressed:text-white " +
                "aria-pressed:enabled:hover:bg-teal-800 " +
                "focus-visible:outline-2 focus-visible:outline-offset-2 " +
                "focus-visible:outline-teal-800";

            button.addEventListener("click", () => {
                selectDate(value);
            });

            days.append(button);
        }
    }

    async function loadMonth() {
        controller?.abort();
        controller = new AbortController();

        const currentRequest = ++requestId;

        loaded = false;
        availableDates = new Set();
        selectedDate = null;

        slots.reset();

        root.dataset.selectedDate = "";
        selectedDateLabel.textContent = "";

        retry.hidden = true;
        status.textContent = copy.loading;

        days.setAttribute("aria-busy", "true");

        renderDays();

        const url = new URL(root.dataset.datesUrl, window.location.origin);

        url.searchParams.set("month", month);
        url.searchParams.set("treatment_id", treatmentId);

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: "application/json",
                },
                credentials: "same-origin",
                cache: "no-store",
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error("Availability request failed.");
            }

            const data = await response.json();

            if (!Array.isArray(data.dates)) {
                throw new Error("Invalid availability response.");
            }

            if (currentRequest !== requestId) {
                return;
            }

            availableDates = new Set(data.dates);
            loaded = true;

            renderDays();

            status.textContent = availableDates.size ? "" : copy.empty;
        } catch (error) {
            if (error.name === "AbortError" || currentRequest !== requestId) {
                return;
            }

            status.textContent = copy.failed;
            retry.hidden = false;
        } finally {
            if (currentRequest === requestId) {
                days.setAttribute("aria-busy", "false");
            }
        }
    }

    function changeMonth(amount) {
        const date = getMonthDate();

        date.setUTCMonth(date.getUTCMonth() + amount);

        const next = date.toISOString().slice(0, 7);

        if (next < initialMonth) {
            return;
        }

        month = next;

        loadMonth();
    }

    root.querySelector("[data-show-calendar]").addEventListener("click", () => {
        const selected = root.querySelector(
            'input[name="treatment_id"]:checked',
        );

        if (!selected) {
            treatmentError.hidden = false;

            treatmentFieldset.setAttribute("aria-invalid", "true");

            treatmentFieldset.setAttribute(
                "aria-describedby",
                "appointment-treatment-description appointment-treatment-error",
            );

            root.querySelector('input[name="treatment_id"]')?.focus();

            return;
        }

        treatmentError.hidden = true;
        treatmentFieldset.removeAttribute("aria-invalid");

        const changed = treatmentId !== selected.value;

        treatmentId = selected.value;

        if (changed) {
            month = initialMonth;
        }

        setStep(2);
        calendarTitle.focus();

        if (changed || !loaded) {
            loadMonth();
        }
    });

    root.querySelector("[data-show-treatments]").addEventListener(
        "click",
        () => {
            setStep(1);

            root.querySelector('input[name="treatment_id"]:checked')?.focus();
        },
    );

    treatmentFieldset.addEventListener("change", () => {
        treatmentError.hidden = true;
        treatmentFieldset.removeAttribute("aria-invalid");

        const selected = root.querySelector(
            'input[name="treatment_id"]:checked',
        );

        if (selected?.value !== treatmentId) {
            controller?.abort();
            requestId++;

            loaded = false;
            availableDates = new Set();
            selectedDate = null;

            root.dataset.selectedDate = "";
            selectedDateLabel.textContent = "";

            slots.reset();
        }

        updateStepDescriptions();
    });

    root.querySelectorAll("[data-go-to-step]").forEach((button) => {
        button.addEventListener("click", () => {
            if (root.dataset.submitting === "true" || root.dataset.currentStep === "5") return;
            const target = Number(button.dataset.goToStep);

            if (target === 1) {
                setStep(1);

                root.querySelector(
                    'input[name="treatment_id"]:checked',
                )?.focus();
            } else if (target === 2) {
                setStep(2);
                calendarTitle.focus();
            } else if (target === 3) {
                setStep(3);
            }
        });
    });

    root.querySelector("[data-show-details]").addEventListener("click", () => {
        if (!selectedDate || !slots.getSelectedSlot()) {
            slotError.hidden = false;

            const firstSlot = calendarStep.querySelector(
                'input[name="starts_at"]',
            );

            (firstSlot ?? calendarTitle).focus();

            return;
        }

        slotError.hidden = true;
        setStep(3);
    });

    root.querySelector("[data-back-calendar]").addEventListener("click", () => {
        setStep(2);
        calendarTitle.focus();
    });

    previousMonth.addEventListener("click", () => {
        changeMonth(-1);
    });

    nextMonth.addEventListener("click", () => {
        changeMonth(1);
    });

    retry.addEventListener("click", loadMonth);

    setStep(1);
    renderWeekdays();
    renderDays();
}
