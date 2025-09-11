<?php

namespace App\Helper;

use Carbon\Carbon;

class CommonHelper
{
    public static function PrintDie($array)
    {
        echo '<pre>';
        print_r($array);
        die();
    }

    public static function formatDate($date): string
    {
        if (!$date) return '';
        return Carbon::parse($date)->format('M d, Y h:i A');

    }
}
