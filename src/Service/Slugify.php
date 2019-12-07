<?php


namespace App\Service;


class Slugify
{
    public function generate(string $input) : string
    {
        // replace non letter or digits by -
        $input = preg_replace('~[^\pL\d]+~u', '-', $input);

        // transliterate
        $input = iconv('utf-8', 'us-ascii//TRANSLIT', $input);

        // remove unwanted characters
        $input = preg_replace('~[^-\w]+~', '', $input);

        // trim
        $input = trim($input, '-');

        // remove duplicate -
        $input = preg_replace('~-+~', '-', $input);

        // lowercase
        $input = strtolower($input);

        if (empty($input)) {
            return 'n-a';
        }

        return $input;
    }
}