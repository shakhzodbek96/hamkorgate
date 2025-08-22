<?php

namespace App\Services\Helpers;

class CompareOwners
{
    /**
     * Compares two name strings for similarity
     * @param string $name1 First name string
     * @param string $name2 Second name string
     * @return float Similarity percentage (0-100)
     */
    static function compareNames($name1, $name2) {
        // Step 1: Normalize and split the input strings
        $name1 = self::convert_to_latin($name1);
        $name2 = self::convert_to_latin($name2);
        $name1 = preg_replace('/[^a-zA-Z\s]/', '', $name1); // Remove non-alphabetic chars
        $name2 = preg_replace('/[^a-zA-Z\s]/', '', $name2);
        // Prepare strings
        $name1 = self::prepareString($name1);
        $name2 = self::prepareString($name2);

        // Convert to uppercase and trim extra spaces
        $name1 = trim(strtoupper(preg_replace('/\s+/', ' ', $name1)));
        $name2 = trim(strtoupper(preg_replace('/\s+/', ' ', $name2)));

        // Split into parts
        $parts1 = explode(' ', $name1);
        $parts2 = explode(' ', $name2);

        $minParts = min(count($parts1), count($parts2));
        // Step 2: Take only the first 3 parts if there are more
        $parts1 = array_slice($parts1, 0, $minParts);
        $parts2 = array_slice($parts2, 0, $minParts);

        // Step 3: Calculate similarity using Levenshtein for each part
        $totalSimilarity = 0;
        $comparisons = 0;

        // Match each part from first name with best match from second name
        foreach ($parts1 as $part1) {
            $bestSimilarity = 0;

            foreach ($parts2 as $part2) {
                // Skip empty parts
                if (empty($part1) || empty($part2)) continue;

                $maxLength = max(strlen($part1), strlen($part2));
                $levenDistance = levenshtein($part1, $part2);
                $partSimilarity = (1 - ($levenDistance / $maxLength)) * 100;

                $bestSimilarity = max($bestSimilarity, $partSimilarity);
            }

            if ($bestSimilarity > 0) {
                $totalSimilarity += $bestSimilarity;
                $comparisons++;
            }
        }

        // Step 4: Calculate overall similarity
        if ($comparisons > 0) {
            $overallSimilarity = $totalSimilarity / $comparisons;
        } else {
            $overallSimilarity = 0;
        }

        // Step 5: Adjust similarity based on difference in total parts
        $partsDiff = abs(count($parts1) - count($parts2));
        if ($partsDiff > 0) {
            // Reduce similarity slightly for different number of name parts
            $overallSimilarity *= (1 - ($partsDiff * 0.05));
        }

        return round($overallSimilarity, 2);
    }

    static function compare_fio(string $fio_1, string $fio_2, $type = 1):int
    {
        $fio_1 = str_replace('  ',' ',$fio_1);
        $fio_2 = str_replace('  ',' ',$fio_2);
        $fio_1 = explode(' ',str_replace('/',' ',$fio_1));
        $fio_2 = explode(' ',str_replace('/',' ',$fio_2));
        $fio_1 = array_slice($fio_1,0,2);
        $fio_2 = array_slice($fio_2,0,2);

        if (count($fio_1) < 2 || count($fio_2) < 2) return -2;
        // first
        $x = self::compare_strings($fio_1[0],$fio_2[0]);
        $y = self::compare_strings($fio_1[1],$fio_2[1]);

        $tmp = $fio_2[0];
        $fio_2[0] = $fio_2[1];
        $fio_2[1] = $tmp;

        $x2 = self::compare_strings($fio_1[0],$fio_2[0]);
        $y2 = self::compare_strings($fio_1[1],$fio_2[1]);

        if (($x+$y) < ($x2 + $y2))
        {
            $x = $x2;
            $y = $y2;
        }

        if ($type == 1)
            return min($x,$y);
        else
            return intval(($x+$y)/2);
    }
    static function convert_to_latin($text):string
    {
        $cyr = [
            'қ','Қ','а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
            'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П',
            'Р', 'С', 'Т', 'У', 'Ў', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'
        ];
        $lat = [
            'q','Q','a', 'b', 'v', 'g', 'd', 'e', 'yo', 'j', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p',
            'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sh', 'a', 'i', 'y', 'e', 'yu', 'ya',
            'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'J', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P',
            'R', 'S', 'T', 'U', 'O', 'F', 'H', 'Ts', 'Ch', 'Sh', 'Sh', 'A', 'I', 'Y', 'e', 'Yu', 'Ya'
        ];
        return str_replace($cyr, $lat, $text);
    }
    static function removeChars($value):string
    {
        $title = str_replace(array('\'', '"',"'","`", ',', ';', '.', '’','-','‘','/','+',')','(',' '), "", $value);
        return $title;
    }

    static function prepareString(string $string):string
    {
        $string = str_replace('kh','h',$string);
        $string = str_replace('dj','j',$string);
        $string = str_replace('q','k',$string);
        $string = str_replace('x','h',$string);
        return $string;
    }

    static function compare_strings(string $a, string $b):int
    {
        $a = strtolower(self::removeChars(self::convert_to_latin($a)));
        $b = strtolower(self::removeChars(self::convert_to_latin($b)));

        if ($a == $b) return 100;

        $exList = [
            "'" => '',
            'u' => 'o',
            'q' => 'k',
            'kh' => 'h',
            'x' => 'h',
            'dj' => 'j',
            'ie' => 'iye',
            'oev' => 'oyev',
            'ae' => 'aye',
        ];

        foreach ($exList as $key => $list) {
            $a = str_ireplace($key,$list,$a);
            $b = str_ireplace($key,$list,$b);

            $a_first = substr($a,0,1);
            $b_first = substr($b,0,2);
            if ($a_first == 'e' && $b_first == 'ye') $a = 'y'.$a;

            $a_first = substr($a,0,2);
            $b_first = substr($b,0,1);
            if ($b_first == 'e' && $a_first == 'ye') $b = 'y'.$b;

        }
        if (substr($a,0,3) != substr($b,0,3))
        {
            return -1;
        }
        else
            return self::compare($a,$b);
    }
    static function compare(string $fio_1, string $fio_2):int
    {
        $min_length = min(strlen($fio_1),strlen($fio_2));
        $fio_1 = str_split(substr($fio_1,0,$min_length));
        $fio_2 = str_split(substr($fio_2,0,$min_length));
        foreach ($fio_1 as $item) {
            foreach ($fio_2 as $key => $val) {
                if ($item == $val){
                    unset($fio_2[$key]);
                    break;
                }
            }
        }

        return 100 - intval(count($fio_2)*100/$min_length);
    }

    static function compare_birth_date($pinfl): string
    {
        if (strlen($pinfl) < 7) return false;

        $centuryCode = substr($pinfl, 0, 1);
        $day = substr($pinfl, 1, 2);
        $month = substr($pinfl, 3, 2);
        $yearLastTwo = substr($pinfl, 5, 2);

        switch ($centuryCode) {
            case '5':
            case '6':
                $century = 2000;
                break;
            case '4':
            case '3':
                $century = 1900;
                break;
            default:
                $century = 2000;
                break;
        }

        $year = $century + (int)$yearLastTwo;

        if (!checkdate((int)$month, (int)$day, $year)) return false;

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
