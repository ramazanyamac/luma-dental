<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('contact.mail.subject') }}</title>

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
        {{ __('contact.mail.intro') }}
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
                                        {{ __('contact.mail.form_label') }}
                                        <br>
                                        #{{ $contactMessage->id }}
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
                                {{ __('contact.mail.heading') }}
                            </h1>

                            <p style="margin:10px 0 0; font-size:14px; line-height:22px; color:#69777b;">
                                {{ __('contact.mail.intro') }}
                            </p>
                        </td>
                    </tr>

                    {{-- İletişim bilgileri --}}
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
                                            {{ __('contact.mail.details') }}
                                        </h2>

                                        <table
                                            role="presentation"
                                            width="100%"
                                            cellspacing="0"
                                            cellpadding="0"
                                            border="0"
                                            style="table-layout:fixed;">
                                            <tr>
                                                <td
                                                    class="detail-label"
                                                    width="35%"
                                                    valign="top"
                                                    style="padding:10px 12px 10px 0; font-size:12px; line-height:20px; color:#65777b;">
                                                    {{ __('contact.form.name') }}
                                                </td>

                                                <td
                                                    class="detail-value"
                                                    valign="top"
                                                    style="padding:10px 0; font-size:14px; line-height:22px; color:#152b3a; font-weight:bold; overflow-wrap:anywhere; word-wrap:break-word;">
                                                    {{ $contactMessage->name }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td
                                                    class="detail-label"
                                                    width="35%"
                                                    valign="top"
                                                    style="padding:10px 12px 10px 0; border-top:1px solid #e8ece9; font-size:12px; line-height:20px; color:#65777b;">
                                                    {{ __('contact.form.email') }}
                                                </td>

                                                <td
                                                    class="detail-value"
                                                    valign="top"
                                                    style="padding:10px 0; border-top:1px solid #e8ece9; font-size:14px; line-height:22px; overflow-wrap:anywhere; word-wrap:break-word;">
                                                    <a
                                                        href="mailto:{{ $contactMessage->email }}"
                                                        style="color:#17646a; text-decoration:underline;">
                                                        {{ $contactMessage->email }}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td
                                                    class="detail-label"
                                                    width="35%"
                                                    valign="top"
                                                    style="padding:10px 12px 10px 0; border-top:1px solid #e8ece9; font-size:12px; line-height:20px; color:#65777b;">
                                                    {{ __('contact.form.phone') }}
                                                </td>

                                                <td
                                                    class="detail-value"
                                                    valign="top"
                                                    style="padding:10px 0; border-top:1px solid #e8ece9; font-size:14px; line-height:22px; color:#152b3a;">
                                                    @if ($contactMessage->phone)
                                                    <a
                                                        href="tel:{{ $contactMessage->phone }}"
                                                        style="color:#17646a; text-decoration:none;">
                                                        {{ $contactMessage->phone }}
                                                    </a>
                                                    @else
                                                    —
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td
                                                    class="detail-label"
                                                    width="35%"
                                                    valign="top"
                                                    style="padding:10px 12px 10px 0; border-top:1px solid #e8ece9; font-size:12px; line-height:20px; color:#65777b;">
                                                    {{ __('contact.mail.topic') }}
                                                </td>

                                                <td
                                                    class="detail-value"
                                                    valign="top"
                                                    style="padding:10px 0; border-top:1px solid #e8ece9; font-size:14px; line-height:22px; color:#152b3a; overflow-wrap:anywhere;">
                                                    {{ __('contact.subject.' . $contactMessage->subject) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Mesaj kartı --}}
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
                                            {{ __('contact.form.message') }}
                                        </h2>

                                        <div style="font-family:Georgia, 'Times New Roman', serif; font-size:18px; line-height:30px; color:#ffffff; overflow-wrap:anywhere; word-wrap:break-word;">
                                            {!! nl2br(e($contactMessage->message)) !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Yanıtla butonu --}}
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
                                            href="mailto:{{ $contactMessage->email }}"
                                            style="display:inline-block; padding:14px 30px; font-size:14px; line-height:22px; font-weight:bold; color:#ffffff; text-decoration:none; overflow-wrap:anywhere;">
                                            {{ __('contact.mail.reply', [
                                                'name' => $contactMessage->name,
                                            ]) }}
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
                                {{ __('contact.mail.footer') }}
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