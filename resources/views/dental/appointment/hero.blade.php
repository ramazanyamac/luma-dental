<div
    class="relative h-[300px] overflow-hidden transition-[height] duration-300 sm:h-[340px] lg:h-[380px] group-data-[compact=true]/booking:h-[144px] motion-reduce:transition-none">
    <img
        src="{{ asset('images/dental/11_faq_clinic_room.png') }}"
        alt=""
        fetchpriority="high"
        class="absolute inset-0 h-full w-full object-cover object-center">

    <div
        class="absolute inset-0 bg-linear-to-r from-[#f8f7f4] via-[#f8f7f4]/85 to-transparent"
        aria-hidden="true"></div>

    <div
        class="relative mx-auto flex h-full max-w-6xl items-center px-6 pb-14 group-data-[compact=true]/booking:hidden">
        <div class="max-w-xl">
            <h1 class="font-serif text-4xl leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                {{ __('appointment.hero.title') }}
            </h1>

            <p class="mt-5 max-w-md text-sm leading-7 text-slate-600 sm:text-base">
                {{ __('appointment.hero.description') }}
            </p>
        </div>
    </div>
</div>