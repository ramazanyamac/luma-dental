@props([
'id',
'name' => 'phone',
'label',
'value' => '',
'required' => false,
])

<div>
    <label
        for="{{ $id }}"
        class="mb-2 block text-sm font-medium text-slate-700">
        {{ $label }}

        @if ($required)
        <span aria-hidden="true">*</span>
        @endif
    </label>

    <div data-input-container class="[&_.iti]:w-full">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="tel"
            value="{{ $value }}"
            autocomplete="tel"
            inputmode="tel"
            data-phone-input
            data-initial-country="tr"
            @required($required)
            class="h-12 w-full rounded-lg border border-stone-200 bg-[#fcfbf9] py-3 pr-4 text-sm text-slate-800 placeholder:text-slate-400 placeholder:opacity-100 outline-none transition focus:border-teal-800 focus:ring-2 focus:ring-teal-800/15">
    </div>
</div>