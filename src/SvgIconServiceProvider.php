<?php

namespace Mrkatz\SvgIcons;

use Illuminate\Support\ServiceProvider;
use Mrkatz\SvgIcons\SvgIcons;
use Illuminate\Support\Facades\Blade;

class SvgIconsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the SvgIcons class to the service container
        $this->app->singleton('svg-icons', function () {
            return new SvgIcons();
        });
    }

    public function boot()
    {
        Blade::directive('svg', function ($expression) {
            $expression = explode(',', $expression);
            $name = trim(str_replace("'", "", $expression[0]));
            $size = count($expression) > 1 ? trim(str_replace("'", "", $expression[1])) : '24';
            $color = count($expression) > 2 ? trim(str_replace("'", "", $expression[2])) : 'currentColor';

            $data = icon($name, $size, $color);
            return "<?php echo '$data'; ?>";
        });
    }
}
