<section class="bg-[#f3f7f5] py-20" aria-labelledby="faq-title">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-2 lg:gap-20">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-700">
                {{ __('faq.eyebrow') }}
            </p>

            <h2 id="faq-title" class="mt-4 font-serif text-4xl text-teal-950">
                {{ __('faq.title') }}
            </h2>

            <p class="mt-4 text-sm leading-7 text-slate-600">
                {{ __('faq.description') }}
            </p>

            <div class="mt-8 divide-y divide-teal-900/10">
                @foreach (['checkups', 'nervous', 'cosmetic', 'booking'] as $question)
                    <details class="group py-4">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-sm font-semibold text-teal-950">
                            {{ __('faq.' . $question . '.question') }}
                            <span class="text-xl text-teal-700 group-open:rotate-45"
                                  aria-hidden="true">+</span>
                        </summary>

                        <p class="pt-3 text-sm leading-6 text-slate-600">
                            {{ __('faq.' . $question . '.answer') }}
                        </p>
                    </details>
                @endforeach
            </div>
        </div>

        <img
            src="{{ asset('images/dental/11_faq_clinic_room.png') }}"
            alt="{{ __('faq.eyebrow') }}"
            class="aspect-[6/5] w-full rounded-3xl object-cover"
            loading="lazy"
        >
    </div>
</section>