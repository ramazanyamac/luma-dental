@extends('dental.layouts.app')

@section('title', __('contact.hero.title') . ' | Luma Dental')

@section('content')
    @include('dental.partials.contact.hero')

    <section class="bg-[#faf9f7] py-10 sm:py-12">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid gap-6 lg:grid-cols-[1.3fr_1fr]">
                @include('dental.partials.contact.form')
                @include('dental.partials.contact.sidebar')
            </div>

            @include('dental.partials.contact.map')
            @include('dental.partials.contact.support')
        </div>
    </section>
@endsection