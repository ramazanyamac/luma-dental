<section id="treatments" class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-700">
                {{ __('treatments.eyebrow') }}
            </p>

            <h2 class="mt-4 font-serif text-4xl text-teal-950">
                {{ __('treatments.title') }}
            </h2>

            <p class="mt-4 text-sm leading-7 text-slate-600">
                {{ __('treatments.intro') }}
            </p>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                'general' => '02_treatment_general_dentistry.png',
                'cosmetic' => '03_treatment_cosmetic_dentistry.png',
                'implants' => '04_treatment_dental_implant.png',
                'orthodontics' => '05_treatment_orthodontics.png'
            ] as $key => $image)
                <article
    id="treatment-{{ $key }}"
    class="flex h-full scroll-mt-8 flex-col overflow-hidden rounded-xl bg-white shadow-[0_8px_30px_rgba(23,49,62,0.05)]"
>
    <img
        src="{{ asset('images/dental/' . $image) }}"
        alt="{{ __('treatments.' . $key . '.title') }}"
        class="aspect-[3/2] w-full object-cover"
        loading="lazy"
    >

    <div class="relative -mt-1 flex flex-1 flex-col rounded-t-xl bg-white p-6">
        <h3 class="font-sans text-xl font-semibold leading-snug text-[#273f4b]">
            {{ __('treatments.' . $key . '.title') }}
        </h3>

        <p class="mt-2 text-base leading-7 text-[#778187]">
            {{ __('treatments.' . $key . '.body') }}
        </p>

        <div class="mt-auto pt-8">
            <a
                href="{{ route('dental.' . app()->getLocale() . '.appointment') }}"
                class="group inline-flex items-center gap-2 text-sm font-semibold text-[#447582] transition-colors hover:text-[#173f4b]"
            >
                {{ __('treatments.learn_more') }}

                <x-heroicon-o-arrow-right
                    class="size-4 shrink-0 transition-transform duration-200 group-hover:translate-x-1"
                    aria-hidden="true"
                />
            </a>
        </div>
    </div>
</article>
            @endforeach
        </div>
    </div>
</section>