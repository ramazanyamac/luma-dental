<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __(($cancelled ?? false) ? (($forClinic ?? false) ? 'appointment.cancellation.clinic_subject' : 'appointment.cancellation.subject') : (($rescheduled ?? false) ? 'appointment.rescheduled.subject' : 'appointment.mail.subject')) }}</title>

    <style>
        @media only screen and (max-width: 600px) {
            .email-outer {
                padding: 20px 12px !important;
            }

            .email-section {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            .email-card {
                padding: 18px !important;
            }

            .email-title {
                font-size: 26px !important;
                line-height: 34px !important;
            }

            .detail-label,
            .detail-value {
                display: block !important;
                width: 100% !important;
            }

            .detail-label {
                padding-bottom: 4px !important;
            }

            .detail-value {
                padding-top: 0 !important;
                border-top: 0 !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background-color:#f6f5f1; font-family:Arial, Helvetica, sans-serif; color:#152b3a;">

    {{-- Gelen kutusunda görünen kısa ön izleme metni --}}
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
        {{ __(($cancelled ?? false) ? (($forClinic ?? false) ? 'appointment.cancellation.clinic_description' : 'appointment.cancellation.description') : (($rescheduled ?? false) ? 'appointment.rescheduled.description' : 'appointment.success.description')) }}
    </div>

    <table
        role="presentation"
        width="100%"
        cellspacing="0"
        cellpadding="0"
        border="0"
        bgcolor="#f6f5f1">
        <tr>
            <td align="center" class="email-outer" style="padding:40px 16px;">

                <!--[if mso]>
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td>
                <![endif]-->

                <table
                    role="presentation"
                    width="100%"
                    cellspacing="0"
                    cellpadding="0"
                    border="0"
                    bgcolor="#ffffff"
                    style="max-width:640px; border:1px solid #e8e6e0; border-radius:16px; border-spacing:0;">
                    {{-- Header --}}
                    <tr>
                        <td
                            class="email-section"
                            style="padding:28px 36px; border-bottom:1px solid #ece9e3;">
                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0">
                                <tr>
                                    <td>
                                        <div style="font-family:Georgia, 'Times New Roman', serif; font-size:26px; line-height:28px; color:#163d43; letter-spacing:2px;">
                                            LUMA
                                        </div>

                                        <div style="margin-top:6px; font-size:10px; color:#5f7477; letter-spacing:4px;">
                                            DENTAL
                                        </div>
                                    </td>

                                    <td
                                        align="right"
                                        style="padding-left:12px; font-size:12px; line-height:20px; color:#7a8587;">
                                        {{ __('appointment.review.appointment') }}

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Başlık ve açıklama --}}
                    <tr>
                        <td
                            class="email-section"
                            style="padding:36px 36px 16px;">
                            <h1
                                class="email-title"
                                style="margin:0; font-family:Georgia, 'Times New Roman', serif; font-size:30px; font-weight:normal; line-height:38px; color:#142c3c;">
                                {{ __(($cancelled ?? false) ? (($forClinic ?? false) ? 'appointment.cancellation.clinic_subject' : 'appointment.cancellation.title') : (($rescheduled ?? false) ? 'appointment.rescheduled.title' : 'appointment.success.title')) }}
                            </h1>

                            <p style="margin:10px 0 0; font-size:14px; line-height:22px; color:#69777b;">
                                {{ ($forClinic ?? false) ? __('management.patient') . ': ' . $appointment->name : __('appointment.mail.greeting', ['name' => $appointment->name]) }}
                                <br>
                                {{ __(($cancelled ?? false) ? (($forClinic ?? false) ? 'appointment.cancellation.clinic_description' : 'appointment.cancellation.description') : (($rescheduled ?? false) ? 'appointment.rescheduled.description' : 'appointment.success.description')) }}
                            </p>
                        </td>
                    </tr>

                    @if ($rescheduled ?? false)
                    <tr><td class="email-section" style="padding:12px 36px; font-size:14px; color:#69777b;">
                        {{ __('appointment.rescheduled.old_time') }}: {{ \Carbon\CarbonImmutable::parse($oldStartsAt)->setTimezone(config('appointments.timezone'))->translatedFormat('d F Y H:i') }}<br>
                        {{ __('appointment.rescheduled.new_time') }}: {{ $appointment->starts_at->setTimezone(config('appointments.timezone'))->translatedFormat('d F Y H:i') }}
                    </td></tr>
                    @endif
                    {{-- Randevu bilgileri --}}
                    <tr>
                        <td
                            class="email-section"
                            style="padding:20px 36px 8px;">
                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                                bgcolor="#f8faf8"
                                style="border:1px solid #e6ebe7; border-radius:12px;">
                                <tr>
                                    <td class="email-card" style="padding:24px;">
                                        <h2 style="margin:0 0 20px; font-family:Georgia, 'Times New Roman', serif; font-size:20px; font-weight:normal; color:#163d43;">
                                            {{ __('appointment.review.appointment') }}
                                        </h2>

                                        <table
                                            role="presentation"
                                            width="100%"
                                            cellspacing="0"
                                            cellpadding="0"
                                            border="0"
                                            style="table-layout:fixed;">
                                            @foreach ([
                                                'treatment' => __('appointment.treatments.' . $appointment->treatment->code),
                                                'datetime' => $appointment->starts_at->setTimezone(config('appointments.timezone'))->translatedFormat('d F Y H:i') . ' (' . config('appointments.timezone') . ')',
                                                'duration' => __('appointment.duration', ['minutes' => $appointment->starts_at->diffInMinutes($appointment->ends_at)]),
                                                'location' => __('appointment.review.address'),
                                            ] as $field => $value)
                                            <tr>
                                                <td
                                                    class="detail-label"
                                                    width="35%"
                                                    valign="top"
                                                    style="padding:10px 12px 10px 0; {{ $loop->first ? '' : 'border-top:1px solid #e8ece9;' }} font-size:12px; line-height:20px; color:#65777b;">
                                                    {{ __('appointment.review.' . $field) }}
                                                </td>

                                                <td
                                                    class="detail-value"
                                                    valign="top"
                                                    style="padding:10px 0; {{ $loop->first ? 'font-weight:bold;' : 'border-top:1px solid #e8ece9;' }} font-size:14px; line-height:22px; color:#152b3a; overflow-wrap:anywhere; word-wrap:break-word;">
                                                    {{ $value }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Randevu referansı --}}
                    <tr>
                        <td
                            class="email-section"
                            style="padding:16px 36px 36px;">
                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                                style="table-layout:fixed;">
                                <tr>
                                    <td
                                        class="email-card"
                                        bgcolor="#163d43"
                                        style="border-radius:12px; padding:26px;">
                                        <h2 style="margin:0 0 10px; font-size:11px; font-weight:normal; line-height:16px; color:#b7cdca; text-transform:uppercase; letter-spacing:1.5px;">
                                            {{ __('appointment.success.reference') }}
                                        </h2>

                                        <div style="font-family:Georgia, 'Times New Roman', serif; font-size:18px; line-height:30px; color:#ffffff; overflow-wrap:anywhere; word-wrap:break-word;">
                                            {{ $appointment->reference }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- İletişim butonu --}}
                    <tr>
                        <td
                            align="center"
                            class="email-section"
                            style="padding:0 36px 36px;">
                            <table
                                role="presentation"
                                cellspacing="0"
                                cellpadding="0"
                                border="0">
                                <tr>
                                    <td
                                        align="center"
                                        bgcolor="#17646a"
                                        style="border-radius:999px; mso-padding-alt:14px 30px;">
                                        <a
                                            href="{{ \App\Support\AppointmentLinks::for($appointment) }}"
                                            style="display:inline-block; padding:14px 30px; font-size:14px; line-height:22px; font-weight:bold; color:#ffffff; text-decoration:none; overflow-wrap:anywhere;">
                                            {{ __('management.manage') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td
                            align="center"
                            class="email-section"
                            bgcolor="#faf9f6"
                            style="padding:24px 36px; border-top:1px solid #ece9e3; border-radius:0 0 16px 16px;">
                            <p style="margin:0; font-size:12px; line-height:20px; color:#69777b;">
                                {{ __(($cancelled ?? false) ? 'appointment.cancellation.footer' : 'appointment.mail.footer') }}
                            </p>

                            <p style="margin:6px 0 0; font-size:11px; line-height:18px; color:#69777b;">
                                © {{ date('Y') }} Luma Dental
                            </p>
                        </td>
                    </tr>
                </table>

                <!--[if mso]>
                        </td>
                    </tr>
                </table>
                <![endif]-->

            </td>
        </tr>
    </table>
</body>

</html>