@extends('admin.layouts.app')

@section('title', __('admin.availability.title'))

@section('content')
<div class="mb-8">
    <a
        href="{{ route('admin.dashboard') }}"
        class="text-sm text-teal-800 underline-offset-4 hover:underline">
        {{ __('admin.availability.back') }}
    </a>

    <h1 class="mt-4 text-3xl font-semibold text-teal-950">
        {{ __('admin.availability.title') }}
    </h1>

    <p class="mt-2 text-sm text-slate-600">
        {{ __('admin.availability.description') }} {{ $timezone }}.
    </p>
</div>

@if (session('success'))
<div
    role="status"
    class="mb-6 rounded-xl border border-teal-200 bg-teal-50 px-5 py-4 text-sm text-teal-900">
    {{ session('success') }}
</div>
@endif

<div class="grid items-start gap-8 lg:grid-cols-[360px_1fr]">
    <section class="rounded-2xl border border-stone-200 bg-white p-6">
        <h2 class="text-lg font-semibold text-teal-950">
            {{ __('admin.availability.add') }}
        </h2>

        <form
            method="POST"
            action="{{ route('admin.availability.store') }}"
            class="mt-6 space-y-5"
            novalidate>
            @csrf

            <div>
                <label for="date" class="block text-sm font-medium">
                    {{ __('admin.fields.date') }}
                </label>

                <input
                    id="date"
                    name="date"
                    type="date"
                    min="{{ $today }}"
                    value="{{ old('date') }}"
                    required
                    aria-invalid="{{ $errors->has('date') ? 'true' : 'false' }}"
                    @error('date') aria-describedby="date-error" @enderror
                    class="mt-2 h-12 w-full rounded-lg border border-stone-300 px-3 outline-none focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15 aria-invalid:border-red-500">

                @error('date')
                <p id="date-error" role="alert" class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div>
                <label for="start_time" class="block text-sm font-medium">
                    {{ __('admin.fields.start_time') }}
                </label>

                <input
                    id="start_time"
                    name="start_time"
                    type="time"
                    step="60"
                    value="{{ old('start_time') }}"
                    required
                    aria-invalid="{{ $errors->has('start_time') ? 'true' : 'false' }}"
                    @error('start_time') aria-describedby="start-time-error" @enderror
                    class="mt-2 h-12 w-full rounded-lg border border-stone-300 px-3 outline-none focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15 aria-invalid:border-red-500">

                @error('start_time')
                <p id="start-time-error" role="alert" class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div>
                <label for="end_time" class="block text-sm font-medium">
                    {{ __('admin.fields.end_time') }}
                </label>

                <input
                    id="end_time"
                    name="end_time"
                    type="time"
                    step="60"
                    value="{{ old('end_time') }}"
                    required
                    aria-invalid="{{ $errors->has('end_time') ? 'true' : 'false' }}"
                    @error('end_time') aria-describedby="end-time-error" @enderror
                    class="mt-2 h-12 w-full rounded-lg border border-stone-300 px-3 outline-none focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15 aria-invalid:border-red-500">

                @error('end_time')
                <p id="end-time-error" role="alert" class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <button
                type="submit"
                class="h-12 w-full rounded-lg bg-teal-900 text-sm font-semibold text-white transition hover:bg-teal-800">
                {{ __('admin.availability.add') }}
            </button>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
        <div class="border-b border-stone-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-teal-950">
                {{ __('admin.availability.periods_title') }}
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                {{ __('admin.availability.periods_description') }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-stone-50 text-slate-600">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-medium">{{ __('admin.fields.date') }}</th>
                        <th scope="col" class="px-6 py-4 font-medium">{{ __('admin.fields.start') }}</th>
                        <th scope="col" class="px-6 py-4 font-medium">{{ __('admin.fields.end') }}</th>
                        <th scope="col" class="px-6 py-4 text-right font-medium">
                            {{ __('admin.actions.title') }}
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-100">
                    @forelse ($windows as $window)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-4">
                            {{ $window->starts_at->setTimezone($timezone)->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $window->starts_at->setTimezone($timezone)->format('H:i') }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $window->ends_at->setTimezone($timezone)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a
                                    href="{{ route('admin.availability.edit', $window) }}"
                                    class="rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-stone-50">
                                    {{ __('admin.actions.edit') }}
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.availability.destroy', $window) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        aria-label="{{ __('admin.availability.remove_label', [
                                        'date' => $window->starts_at
                                            ->setTimezone($timezone)
                                            ->format('d.m.Y H:i'),
                                    ]) }}"
                                        class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50">
                                        {{ __('admin.actions.remove') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                            {{ __('admin.availability.empty') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($windows->hasPages())
        <div class="border-t border-stone-200 px-6 py-4">
            {{ $windows->links() }}
        </div>
        @endif
        @error('availability')
        <div
            role="alert"
            class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
            {{ $message }}
        </div>
        @enderror
    </section>
</div>
@endsection