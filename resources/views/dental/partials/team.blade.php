<section id="team" class="bg-[#fafaf8] py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-6">
        {{-- Bölüm başlığı --}}
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#7c8d94]">
                {{ __('team.eyebrow') }}
            </p>

            <h2 class="mt-4 font-serif text-4xl leading-tight tracking-tight text-[#17313e] lg:text-5xl">
                {{ __('team.title') }}
            </h2>

            <p class="mt-4 text-base leading-7 text-[#778187]">
                {{ __('team.description') }}
            </p>
        </div>

        {{-- Ekip kartları --}}
        <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
            'general' => '07_team_portrait_1.png',
            'cosmetic' => '08_team_portrait_2.png',
            'orthodontics' => '09_team_portrait_3.png',
            'implants' => '10_team_portrait_4.png'
            ] as $role => $image)
            <article class="overflow-hidden rounded-xl bg-white shadow-[0_8px_30px_rgba(23,49,62,0.04)]">
                <img
                    src="{{ asset('images/dental/' . $image) }}"
                    alt="{{ __('team.member') }}"
                    class="aspect-[7/6] w-full object-cover object-top"
                    loading="lazy">

                <div class="relative -mt-1 flex items-center justify-between gap-3 rounded-t-lg bg-white px-5 py-5">
                    <div>
                        <h3 class="text-sm font-semibold text-[#273f4b]">
                            {{ __('team.member') }}
                        </h3>

                        <p class="mt-1 text-sm leading-5 text-[#778187]">
                            {{ __('team.' . $role) }}
                        </p>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Ekip sayfası oluşturulduğunda bağlantıya dönüştürülecek --}}
        <div class="mt-8 text-center">
            <a
                href="#team"
                class="group mt-9 inline-flex items-center justify-center gap-3 rounded-full border-2 border-[#b5c6cb] px-8 py-3.5 text-sm font-semibold text-[#355866] transition-colors hover:border-[#355866] hover:bg-white">
                {{ __('team.view_all') }}

                <x-heroicon-o-arrow-right
                    class="size-4 transition-transform duration-200 group-hover:translate-x-1"
                    aria-hidden="true" />
            </a>
        </div>
    </div>
</section>