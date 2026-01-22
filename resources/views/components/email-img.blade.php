@props([
    // Source & accessibility
    'src' => null, // can be passed as prop or via <x-slot:src>
    'alt' => '', // can be passed as prop or via <x-slot:alt>

    // Sizing
    'width' => null, // number in px (recommended)
    'height' => null, // number in px (optional)
    'maxWidth' => 600, // px clamp for responsiveness

    // Layout
    'center' => true, // centers image inside container
    'paddingTop' => 0, // px
    'paddingBottom' => 0, // px
    'paddingX' => 0, // px (applies to td)

    // Rendering
    'fluid' => true, // makes image responsive (width:100%; max-width:...)
    'display' => 'block', // block is safest for email
    'link' => null, // optional wrap in <a>
    'target' => '_blank',
    'rel' => 'noopener noreferrer',
])

@php
    // Allow src/alt to be provided via named slots:
    $slotSrc = isset($src) ? $src : null;
    $slotAlt = isset($alt) ? $alt : null;

    // If someone uses <x-slot:src> / <x-slot:alt>, those will appear as variables $srcSlot / $altSlot
    // but Blade doesn't auto-create them, so we keep it simple:
// Most usage should pass props. Still, we accept slot content fallback.
$resolvedSrc = $slotSrc ?: trim($src ?? '');
$resolvedAlt = $slotAlt ?? '';

// If they passed <x-email-image>...</x-email-image> with content, treat that as src fallback.
$fallbackFromDefaultSlot = trim($slot ?? '');
if (!$resolvedSrc && $fallbackFromDefaultSlot) {
    $resolvedSrc = $fallbackFromDefaultSlot;
}

$w = is_numeric($width) ? (int) $width : null;
$h = is_numeric($height) ? (int) $height : null;
$mw = is_numeric($maxWidth) ? (int) $maxWidth : 600;

$align = $center ? 'center' : 'left';

    // Build style string for the <img> tag (email-safe)
    $imgStyle = "display:{$display}; border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic;";
    if ($fluid) {
        // responsive image inside container
        $imgStyle .= " width:100%; max-width:{$mw}px; height:auto;";
    }

    // If explicit width provided, also set width attr for Outlook reliability
    // but keep fluid styling if enabled.

@endphp

@if ($resolvedSrc)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
        <tr>
            <td align="{{ $align }}"
                style="
                padding-top: {{ (int) $paddingTop }}px;
                padding-bottom: {{ (int) $paddingBottom }}px;
                padding-left: {{ (int) $paddingX }}px;
                padding-right: {{ (int) $paddingX }}px;
            ">
                @if ($link)
                    <a href="{{ $link }}" target="{{ $target }}" rel="{{ $rel }}"
                        style="text-decoration:none; border:0; display:inline-block;">
                @endif

                <img src="{{ $resolvedSrc }}" alt="{{ $resolvedAlt }}"
                    @if ($w) width="{{ $w }}" @endif
                    @if ($h) height="{{ $h }}" @endif style="{{ $imgStyle }}">

                @if ($link)
                    </a>
                @endif
            </td>
        </tr>
    </table>
@endif
