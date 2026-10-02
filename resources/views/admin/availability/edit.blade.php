@extends('admin.layouts.app')

@section('title', 'Edit availability')

@section('content')
<div class="mx-auto max-w-lg">
    <a
        href="{{ \App\Support\AdminUrl::route('admin.availability.index') }}"
        class="text-sm text-teal-800 underline-offset-4 hover:underline">
        {{ __('admin.availability.back') }} </a>

    <section class="mt-6 rounded-2xl border border-stone-200 bg-white p-6 sm:p-8">
        <h1 class="text-2xl font-semibold text-teal-950">
            {{ __('admin.availability.edit_title') }}
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            {{ __('admin.common.timezone') }}
        </p>

        @error('availability')
        <div
            role="alert"
            class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ $message }}
        </div>
        @enderror

        @php
        $fields = [
        'date' => [
        'label' => 'admin.fields.date',
        'type' => 'date',
        'value' => $window->starts_at
        ->setTimezone($timezone)
        ->format('Y-m-d'),
        ],
        'start_time' => [
        'label' => 'admin.fields.start_time',
        'type' => 'time',
        'value' => $window->starts_at
        ->setTimezone($timezone)
        ->format('H:i'),
        ],
        'end_time' => [
        'label' => 'admin.fields.end_time',
        'type' => 'time',
        'value' => $window->ends_at
        ->setTimezone($timezone)
        ->format('H:i'),
        ],
        ];
        @endphp

        <form
            method="POST"
            action="{{ route('admin.availability.update', $window) }}"
            class="mt-6 space-y-5"
            novalidate>
            @csrf
            @method('PATCH')

            @foreach ($fields as $name => $field)
            <div>
                <label for="{{ $name }}" class="block text-sm font-medium">
                    {{ __($field['label']) }}
                </label>

                <input
                    id="{{ $name }}"
                    name="{{ $name }}"
                    type="{{ $field['type'] }}"
                    value="{{ old($name, $field['value']) }}"
                    required
                    @if ($field['type']==='date' )
                    min="{{ $today }}"
                    @else
                    step="60"
                    @endif
                    aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}"
                    @if ($errors->has($name))
                aria-describedby="{{ $name }}-error"
                @endif
                class="mt-2 h-12 w-full rounded-lg border border-stone-300 px-3 outline-none focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15 aria-invalid:border-red-500"
                >

                @error($name)
                <p
                    id="{{ $name }}-error"
                    role="alert"
                    class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>
            @endforeach

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    class="rounded-lg bg-teal-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-800">
                    {{ __('admin.actions.save') }}
                </button>

                <a
                    href="{{ \App\Support\AdminUrl::route('admin.availability.index') }}"
                    class="text-sm font-medium text-slate-600 underline-offset-4 hover:underline">
                    {{ __('admin.actions.cancel') }}
                </a>
            </div>
        </form>
    </section>
</div>
@endsection