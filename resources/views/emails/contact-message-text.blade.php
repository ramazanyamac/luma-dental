{!! __('contact.mail.heading') !!}

{!! __('contact.mail.intro') !!}

{!! __('contact.mail.reference') !!}: #{!! $contactMessage->id !!}
{!! __('contact.form.name') !!}: {!! $contactMessage->name !!}
{!! __('contact.form.email') !!}: {!! $contactMessage->email !!}
{!! __('contact.form.phone') !!}: {!! $contactMessage->phone ?: '—' !!}
{!! __('contact.mail.topic') !!}: {!! __('contact.subject.' . $contactMessage->subject) !!}

{!! __('contact.form.message') !!}:
{!! $contactMessage->message !!}

{!! __('contact.mail.reply_hint') !!}

Luma Dental