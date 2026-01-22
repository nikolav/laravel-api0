 @props([
     'url' => '#',
     'label' => null,

     // Visuals
     'bg' => '#4f46e5',
     'color' => '#ffffff',
     'radius' => 10, // px
     'paddingY' => 12, // px
     'paddingX' => 18, // px

     // Typography
     'fontFamily' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif',
     'fontSize' => 16, // px
     'fontWeight' => 700,

     // Sizing / alignment
     'align' => 'center', // left|center|right
     'fullWidth' => false,

     // VML sizing (Outlook). If fullWidth=true, this is ignored.
     'width' => 260, // px

     // Extras
     'target' => '_blank',
     'rel' => 'noopener noreferrer',
 ])

 @php
     $text = $label ?? trim($slot ?? '') ?: 'Open';
     $href = $url;

     $pad = $paddingY . 'px ' . $paddingX . 'px';
     $borderRadius = (int) $radius;

     // Approx VML height: fontSize + vertical padding*2 + small fudge
     $vmlHeight = (int) ($fontSize + $paddingY * 2 + 6);

     // If full width: set a big VML width (Outlook will clamp to container)
     $vmlWidth = $fullWidth ? 600 : (int) $width;

     // Ensure align values are sane
     $align = in_array($align, ['left', 'center', 'right'], true) ? $align : 'center';
 @endphp

 <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="{{ $fullWidth ? '100%' : 'auto' }}"
     style="border-collapse:separate; mso-table-lspace:0pt; mso-table-rspace:0pt;">
     <tr>
         <td align="{{ $align }}" style="padding: 16px 0;">
             <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml"
                href="{{ $href }}"
                style="height:{{ $vmlHeight }}px; v-text-anchor:middle; width:{{ $vmlWidth }}px;"
                arcsize="{{ max(1, min(100, (int) round(($borderRadius / max(1, $vmlHeight)) * 100))) }}%"
                strokecolor="{{ $bg }}"
                fillcolor="{{ $bg }}">
                <w:anchorlock/>
                <center style="color:{{ $color }}; font-family:{{ $fontFamily }}; font-size:{{ $fontSize }}px; font-weight:{{ $fontWeight }};">
                    {{ $text }}
                </center>
            </v:roundrect>
            <![endif]-->

             <!--[if !mso]><!-- -->
             <a href="{{ $href }}" target="{{ $target }}" rel="{{ $rel }}"
                 style="
                    display: {{ $fullWidth ? 'block' : 'inline-block' }};
                    {{ $fullWidth ? 'width:100%;' : '' }}
                    background: {{ $bg }};
                    color: {{ $color }};
                    font-family: {{ $fontFamily }};
                    font-size: {{ $fontSize }}px;
                    font-weight: {{ $fontWeight }};
                    line-height: {{ $fontSize + 4 }}px;
                    text-align: center;
                    text-decoration: none;
                    border-radius: {{ $borderRadius }}px;
                    padding: {{ $pad }};
                    mso-padding-alt: 0;
                    -webkit-text-size-adjust: none;
                ">
                 {{ $text }}
             </a>
             <!--<![endif]-->
         </td>
     </tr>
 </table>
