<?php

namespace Mrkatz\SvgIcons;

class SvgIcons
{
    private static $icons;

    public static function getIcon($name, $size = '24', $color = 'currentColor')
    {
        if (self::$icons === null) {
            self::$icons = include __DIR__ . '/svg-icons.php';
        }

        if (!isset(self::$icons[$name])) {
            return '';
        }

        $iconContent = self::$icons[$name];

        return self::updateSvgAttributes($iconContent, $size, $color);
    }

    private static function updateSvgAttributes($svgContent, $size, $color)
    {
        $color = preg_match('/^#[0-9A-Fa-f]{6}$/i', $color) || preg_match('/^[a-zA-Z]+$/', $color) ? $color : 'currentColor';

        $svgContent = preg_replace('/<svg([^>]*)fill\s*=\s*["\'][^"\']*["\']([^>]*)>/i', '<svg$1$2>', $svgContent);

        $svgContent = preg_replace(
            '/<svg([^>]*)>/i',
            '<svg$1 width="' . $size . '" height="' . $size . '" fill="' . $color . '">',
            $svgContent
        );

        $svgContent = preg_replace(
            '/(<path[^>]*)fill\s*=\s*["\'][^"\']*["\']?/i',
            '$1 fill="' . $color . '"',
            $svgContent
        );

        return trim($svgContent);
    }
}
