<?php
namespace App\Helper;

use Illuminate\Support\Facades\URL;

/**
 * Class Icon for displaying available file icons
 * @package App\Helper
 */
class Icon
{
    private static $defaultIcon = "default";

    public static function get($name)
    {
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        if(file_exists(public_path('icons').'/'.$ext.'.png'))
        {
            return URL::to('public/icons').'/'.$ext.'.png';
        }

        return URL::to('public/icons').'/'.self::$defaultIcon.'.png';
    }
}
