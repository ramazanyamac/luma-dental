<section class="border-b border-slate-100 bg-white"
         aria-label="{{ __('features.label') }}">
    <div class="mx-auto grid max-w-7xl gap-8 px-6 py-10 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            'technology' => 'iconpark-teeth',
            'personal' => 'bi-heart',
            'team' => 'pepicon-people',
            'confidence' => 'akar-leaf'
        ] as $key => $icon)
            <div class="flex items-start gap-4">
            <x-dynamic-component
                :component="$icon"
                class="size-8 shrink-0 text-teal-800"
                aria-hidden="true"
            />

            <div>
                <h2 class="text-sm font-semibold text-teal-950">
                    {{ __("features.$key.title") }}
                </h2>

                <p class="mt-1 text-xs leading-5 text-slate-600">
                    {{ __("features.$key.body") }}
                </p>
                </div>
            </div>
        @endforeach
    </div>
</section>