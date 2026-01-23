@props([
    'title' => null,
    // Optional slots/props
    'preheader' => null, // hidden preview text (shows in inbox)
    'lang' => null, // override locale if needed
    'backgroundColor' => '#f3f4f6',
    'containerBg' => '#ffffff',
    'brandColor' => '#4f46e5', // accessible-ish indigo
    'logoUrl' => null,
    'logoAlt' => 'Logo',
    'footerText' => null,
])

@php
    $docLang = $lang ?: str_replace('_', '-', app()->getLocale());
    $safeTitle = $title ?: config('app.name');
@endphp

<!doctype html>
<html lang="{{ $docLang }}" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">

    @if ($safeTitle)
        <title>{{ $safeTitle }}</title>
    @endif

    <!--[if mso]>
  <noscript>
      <xml>
          <o:OfficeDocumentSettings>
              <o:PixelsPerInch>96</o:PixelsPerInch>
              <o:AllowPNG/>
          </o:OfficeDocumentSettings>
      </xml>
  </noscript>
  <![endif]-->

    <style>
        /* Email-safe reset */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse !important;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
            display: block;
            height: auto;
            line-height: 100%;
        }

        a {
            text-decoration: none;
        }

        /* Layout helpers */
        .wrapper {
            width: 100%;
            background: {{ $backgroundColor }};
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .px {
            padding-left: 24px;
            padding-right: 24px;
        }

        .py {
            padding-top: 24px;
            padding-bottom: 24px;
        }

        .card {
            background: {{ $containerBg }};
            border-radius: 12px;
        }

        .h1 {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 22px;
            line-height: 30px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .text {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 16px;
            line-height: 24px;
            color: #374151;
        }

        .muted {
            color: #6b7280;
            font-size: 13px;
            line-height: 20px;
        }

        /* Button (bulletproof-ish) */
        .btn td {
            border-radius: 10px;
            background: {{ $brandColor }};
        }

        .btn a {
            display: inline-block;
            padding: 12px 18px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
        }

        /* Mobile */
        @media screen and (max-width: 600px) {
            .px {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }

            .py {
                padding-top: 18px !important;
                padding-bottom: 18px !important;
            }

            .h1 {
                font-size: 20px !important;
                line-height: 28px !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background: {{ $backgroundColor }};">
    {{-- Preheader text (hidden) --}}
    <div
        style="display:none; font-size:1px; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; mso-hide:all;">
        {{ $preheader ?? '' }}
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>

    <table role="presentation" class="wrapper" width="100%" cellpadding="0" cellspacing="0"
        style="background: {{ $backgroundColor }};">
        <tr>
            <td align="center" style="padding: 24px 0;">
                <table role="presentation" class="container" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width: 600px;">
                    <tr>
                        <td class="px">
                            {{-- Header --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="left" style="padding: 8px 0 16px 0;">
                                        @if ($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $logoAlt }}" width="140"
                                                style="width:140px; max-width:140px;">
                                        @endif
                                    </td>
                                </tr>

                                @if ($header)
                                    <tr>
                                        <td class="text" style="padding: 0 0 8px 0;">
                                            <h1 class="h1">{{ $header }}</h1>
                                        </td>
                                    </tr>
                                @endif

                                @isset($subHeader)
                                    <tr>
                                        <td class="text" style="padding: 0 0 8px 0;">
                                            {{ $subHeader }}
                                        </td>
                                    </tr>
                                @endisset
                            </table>

                            {{-- Card / Body --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="card"
                                style="background: {{ $containerBg }}; border-radius: 12px;">
                                <tr>
                                    <td class="px py text" style="padding:24px;">
                                        {{-- Main content --}}
                                        {{ $content ?? $slot }}
                                    </td>
                                </tr>
                            </table>

                            {{-- Footer --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="px muted" style="padding: 16px 24px 0 24px; text-align:center;">
                                        @isset($footer)
                                            {{ $footer }}
                                        @else
                                            {{ $footerText ?? config('app.name') . ' · ' . now()->year }}
                                        @endisset
                                    </td>
                                </tr>

                                {{-- Spacer --}}
                                <tr>
                                    <td style="height: 24px; line-height: 24px;">&nbsp;</td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
