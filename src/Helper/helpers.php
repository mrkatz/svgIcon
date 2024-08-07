<?php

use Mrkatz\SvgIcons\SvgIcons;


if (!function_exists('icon')) {
    function icon($name, $size = '24', $color = 'currentColor')
    {
        return app('App\Helpers\SvgIcons')::getIcon($name, $size, $color);
    }
}
