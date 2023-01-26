<?php

namespace App\Services;

use Stevebauman\Purify\Facades\Purify;

class TextInputFilterService
{
    public static function storeFilter(string $input)
    {
        return strip_tags(htmlspecialchars_decode(Purify::clean($input)));
    }

    public static function displayFilter(string $input)
    {
        return Purify::clean($input);
    }
}
