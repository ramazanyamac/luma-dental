<h2 data-review-title tabindex="-1" class="font-serif text-3xl text-teal-950 outline-none sm:text-4xl">{{ __('appointment.review.title') }}</h2>
<p class="mt-3 text-sm leading-6 text-slate-500">{{ __('appointment.review.description') }}</p>
<div class="mt-7 grid gap-5 lg:grid-cols-2">
<section class="rounded-xl border border-stone-200 bg-stone-50/50 p-5 sm:p-6">
<div class="flex items-center justify-between gap-4">
<h3 class="font-serif text-xl text-teal-950">{{ __('appointment.review.appointment') }}</h3>
<button type="button" data-review-edit="2" class="text-sm font-medium text-teal-800 underline underline-offset-4">{{ __('appointment.review.edit') }} <span class="sr-only">{{ __('appointment.review.appointment') }}</span></button>
</div><dl class="mt-6 space-y-4">
<div><dt class="text-xs text-slate-500">{{ __('appointment.review.treatment') }}</dt><dd data-review-value="treatment" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.review.datetime') }}</dt><dd data-review-value="datetime" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.review.duration') }}</dt><dd data-review-value="duration" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.review.location') }}</dt><dd data-review-value="location" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">{{ __('appointment.review.address') }}</dd></div>
</dl></section>
<section class="rounded-xl border border-stone-200 bg-stone-50/50 p-5 sm:p-6">
<div class="flex items-center justify-between gap-4">
<h3 class="font-serif text-xl text-teal-950">{{ __('appointment.review.personal') }}</h3>
<button type="button" data-review-edit="3" class="text-sm font-medium text-teal-800 underline underline-offset-4">{{ __('appointment.review.edit') }} <span class="sr-only">{{ __('appointment.review.personal') }}</span></button>
</div><dl class="mt-6 space-y-4">
<div><dt class="text-xs text-slate-500">{{ __('appointment.fields.name') }}</dt><dd data-review-value="name" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.fields.email') }}</dt><dd data-review-value="email" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.fields.phone') }}</dt><dd data-review-value="phone" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.fields.birth_date') }}</dt><dd data-review-value="birth_date" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.fields.gender') }}</dt><dd data-review-value="gender" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.fields.reason') }}</dt><dd data-review-value="reason" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.fields.new_patient') }}</dt><dd data-review-value="is_new_patient" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
<div><dt class="text-xs text-slate-500">{{ __('appointment.fields.notes') }}</dt><dd data-review-value="notes" class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">—</dd></div>
</dl></section>
</div>
<p data-booking-error role="alert" class="mt-5 text-sm text-red-700" hidden></p>
<div class="mt-7 flex flex-wrap items-center justify-between gap-4">
<button type="button" data-review-edit="3" class="rounded-full border border-stone-300 px-6 py-3 text-sm font-semibold text-slate-700">{{ __('appointment.navigation.back') }}</button>
<button type="button" data-confirm-booking class="rounded-full bg-teal-900 px-7 py-3 text-sm font-semibold text-white transition hover:bg-teal-800 disabled:cursor-wait disabled:opacity-60">{{ __('appointment.review.confirm') }} <span aria-hidden="true">→</span></button>
</div>
<script type="application/json" id="booking-copy">{!! json_encode([
    'saving' => __('appointment.review.saving'),
    'confirm' => __('appointment.review.confirm'),
    'failed' => __('appointment.booking.failed'),
    'expired' => __('appointment.booking.expired'),
    'yes' => __('appointment.review.yes'),
    'no' => __('appointment.review.no'),
    'duration' => __('appointment.duration', ['minutes' => ':minutes']),
    'location' => __('appointment.review.address'),
    'emailQueuedTitle' => __('operations.queued_title'),
    'emailQueued' => __('operations.queued_patient'),
    'emailSent' => __('appointment.success.email_sent'),
    'emailFailed' => __('appointment.success.email_failed'),
    'emailSentTitle' => __('appointment.success.email_title'),
    'emailFailedTitle' => __('appointment.success.email_failed_title'),
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
