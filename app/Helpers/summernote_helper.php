<?php

if (!function_exists('bersihkanSummernoteHTML')) {
    function bersihkanSummernoteHTML(string $html): string
    {
        // Hapus conditional comments MS Office
        $html = preg_replace('/<!--\[if [^\]]+\]>.*?<!\[endif\]-->/is', '', $html);

        // Hapus style yg bermasalah
        $html = preg_replace_callback('/style="([^"]*)"/i', function ($match) {
            $style = $match[1];
            $clean = [];

            foreach (explode(';', $style) as $rule) {
                $rule = trim($rule);
                if (
                    stripos($rule, 'color') === 0 ||
                    stripos($rule, 'font-family') === 0 ||
                    stripos($rule, 'background-color') === 0 ||
                    stripos($rule, 'mso-') === 0
                ) {
                    continue;
                }
                if ($rule !== '') {
                    $clean[] = $rule;
                }
            }

            return count($clean) ? 'style="' . implode('; ', $clean) . '"' : '';
        }, $html);

        // Hapus tag Word khusus
        $html = preg_replace('/<\/?(w|o|xml)(:[^>]*)?>/i', '', $html);

        // Hapus atribut aneh dengan mso
        $html = preg_replace('/\s?(class|lang|style)="[^"]*(mso|Mso)[^"]*"/i', '', $html);

        // Bersihkan tag font dan span
        $html = preg_replace('/<font[^>]*>(.*?)<\/font>/i', '$1', $html);
        $html = preg_replace('/<span[^>]*>(.*?)<\/span>/i', '$1', $html);
        $html = preg_replace('/<span[^>]*>\s*<\/span>/i', '', $html);
        $html = preg_replace('/<div[^>]*>\s*<\/div>/i', '', $html);

        // Hapus tag kosong
        $html = preg_replace('/<([a-z]+)[^>]*>\s*<\/\1>/i', '', $html);

        // Hapus <p><br></p> kosong
        $html = preg_replace('/<p>(?:\s|&nbsp;)*<br\s*\/?>(?:\s|&nbsp;)*<\/p>/i', '', $html);

        // Hapus atribut align
        $html = preg_replace('/\s*align="[^"]*"/i', '', $html);

        // Ganti <b> ke <strong>
        $html = preg_replace('/<b>(.*?)<\/b>/i', '<strong>$1</strong>', $html);

        // Ganti <i> ke <em>
        $html = preg_replace('/<i>(.*?)<\/i>/i', '<em>$1</em>', $html);
        
        // Hapus sisa komentar dan <br> kosong di akhir
        $html = preg_replace('/<p><!--EndFragment--><br\s*\/?><\/p>/i', '', $html);
        
        // Hapus paragraf kosong, termasuk yg cuma ada <br>
        $html = preg_replace('/<p>(?:\s|&nbsp;|<br\s*\/?>)*<\/p>/i', '', $html);


        return $html;
    }
}


