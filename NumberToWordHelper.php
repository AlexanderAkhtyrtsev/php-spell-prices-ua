<?php


namespace api\helpers;


class NumberToWordHelper
{

    /**
     * @param $num [number]
     * @return string
     */
    public static function spell($num): string
    {
        $nul = 'нуль';
        $ten = array(
            array('', 'один', 'два', 'три', 'чотири', 'п`ять', 'шість', 'сім', 'восім', 'дев`ять'),
            array('', 'одна', 'дві', 'три', 'чотири', 'п`ять', 'шість', 'сім', 'восім', 'дев`ять'),
        );
        $a20 = array('десять', 'одинадцять', 'дванадцать', 'тринадцять', 'чотирнадцять', 'п`ятнадцать', 'шістнадцять', 'сімнадцять', 'вісімнадцять', 'дев`ятнадцять');
        $tens = array(2 => 'двадцять', 'тридцять', 'сорок', 'п`ятдесят', 'шістдесят', 'сімдесят', 'вісімдесят', 'дев`яносто');
        $hundred = array('', 'сто', 'двісті', 'триста', 'чотириста', 'п`ятсот', 'шістсот', 'сімсот', 'вісімсот', 'де`вятсот');
        $unit = array( // Units
            array('копійка', 'копійки', 'копійок', 1),
            array('гривня', 'гривні', 'гривень', 1),
            array('тисяча', 'тисячі', 'тисяч', 1),
            array('мільйон', 'мільйона', 'мільйоній', 0),
            array('мільйард', 'мільйарда', 'мільйардів', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = self::morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        $out[] = self::morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop . ' ' . self::morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    private static function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }
}
