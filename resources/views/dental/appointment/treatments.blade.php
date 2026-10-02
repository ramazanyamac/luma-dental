<fieldset
    id="appointment-treatment-selection"
    aria-describedby="appointment-treatment-description">
    <legend class="font-serif text-3xl text-teal-950 sm:text-4xl">
        {{ __('appointment.treatment.title') }}
    </legend>

    <p
        id="appointment-treatment-description"
        class="mt-3 text-sm leading-6 text-slate-500 sm:text-base">
        {{ __('appointment.treatment.description') }}
    </p>

    <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4 lg:gap-5">
        @foreach ($treatments as $treatment)
        <div class="relative min-w-0">
            <input
                id="treatment-{{ $treatment->id }}"
                name="treatment_id"
                type="radio"
                value="{{ $treatment->id }}"
                data-duration="{{ $treatment->duration_minutes }}"
                data-treatment-name="{{ __('appointment.treatments.' . $treatment->code) }}"
                class="peer sr-only"
                required
                @checked(
                (string) old('treatment_id')===(string) $treatment->id
            )
            >

            <label
                for="treatment-{{ $treatment->id }}"
                class="block h-full cursor-pointer overflow-hidden rounded-xl border border-stone-200 bg-white transition duration-200 hover:border-teal-600 hover:shadow-sm peer-checked:border-teal-800 peer-checked:bg-teal-50/40 peer-checked:ring-1 peer-checked:ring-teal-800 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-4 peer-focus-visible:outline-teal-800">
                @if ($treatment->image_path)
                <img
                    src="{{ asset($treatment->image_path) }}"
                    alt=""
                    width="640"
                    height="480"
                    loading="lazy"
                    class="aspect-[4/3] w-full object-cover">
                @endif

                <span class="block px-4 py-5 text-center">
                    <span class="block font-serif text-lg leading-snug text-teal-950 sm:text-xl">
                        {{ __('appointment.treatments.' . $treatment->code) }}
                    </span>

                    <span class="mt-2 block text-xs text-slate-500 sm:text-sm">
                        {{ __('appointment.duration', [
                                'minutes' => $treatment->duration_minutes,
                            ]) }}
                    </span>
                </span>
            </label>

            <span
                aria-hidden="true"
                class="pointer-events-none absolute right-3 top-3 flex size-7 items-center justify-center rounded-full bg-teal-900 text-white opacity-0 shadow-sm transition-opacity peer-checked:opacity-100">
                <x-heroicon-o-check class="size-4" />
            </span>
        </div>
        @endforeach
    </div>
</fieldset>