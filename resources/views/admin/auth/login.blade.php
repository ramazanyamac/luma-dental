@extends('admin.layouts.app')

@section('title', __('admin.auth.sign_in'))

@section('content')
<section class="mx-auto max-w-md rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
    <p class="text-xs font-semibold uppercase tracking-widest text-teal-700">
        {{ __('admin.auth.eyebrow') }}
    </p>

    <h1 class="mt-3 text-3xl font-semibold text-teal-950">
        {{ __('admin.auth.title') }}
    </h1>

    <p class="mt-2 text-sm leading-6 text-slate-500">
        {{ __('admin.auth.description') }}
    </p>

    <form
        method="POST"
        action="{{ route('admin.login.store') }}"
        class="mt-8 space-y-5"
        novalidate>
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium">
                {{ __('admin.fields.email') }}
            </label>

            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                autocomplete="username"
                required
                autofocus
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                @error('email') aria-describedby="email-error" @enderror
                @class([ 'mt-2 h-12 w-full rounded-lg border bg-white px-4 outline-none transition focus:ring-2' , 'border-red-500 focus:ring-red-100'=> $errors->has('email'),
            'border-stone-300 focus:border-teal-800 focus:ring-teal-800/15' => !$errors->has('email'),
            ])
            >

            @error('email')
            <p id="email-error" role="alert" class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium">
                {{ __('admin.fields.password') }}
            </label>

            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                @error('password') aria-describedby="password-error" @enderror
                @class([ 'mt-2 h-12 w-full rounded-lg border bg-white px-4 outline-none transition focus:ring-2' , 'border-red-500 focus:ring-red-100'=> $errors->has('password'),
            'border-stone-300 focus:border-teal-800 focus:ring-teal-800/15' => !$errors->has('password'),
            ])
            >

            @error('password')
            <p id="password-error" role="alert" class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

        <button
            type="submit"
            class="h-12 w-full rounded-lg bg-teal-900 text-sm font-semibold text-white transition hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-900">
            {{ __('admin.auth.sign_in') }}
        </button>
    </form>
</section>
@endsection