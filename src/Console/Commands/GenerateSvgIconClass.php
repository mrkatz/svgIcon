<?php

namespace Mrkatz\SvgIcons\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSvgIconClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'svg:generate-class {--output=App/Helpers/SvgIcons.php}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a PHP class containing all SVG icons';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $outputPath = $this->option('output');
        $iconsDir = resource_path('icons');
        $className = 'SvgIcons';

        $svgFiles = File::allFiles($iconsDir);

        // Start building the class content
        $classContent = "<?php\n\nnamespace App\\Helpers;\n\nclass $className\n{\n";

        // Initialize the static array to store SVG contents
        $classContent .= "    private static \$icons = [\n";

        foreach ($svgFiles as $file) {
            $relativePath = $file->getRelativePathname();
            $iconName = pathinfo($relativePath, PATHINFO_FILENAME);
            $directoryName = $file->getRelativePath();
            $formattedName = str_replace(DIRECTORY_SEPARATOR, '_', $directoryName) . '_' . str_replace('-', '_', $iconName);

            $svgContent = $file->getContents();
            $svgContent = str_replace(["\r", "\n"], ['\\r', '\\n'], $svgContent); // Convert newlines to escape sequences
            $escapedSvgContent = str_replace(["\\n"], '', $svgContent); // Remove newline characters
            $escapedSvgContent = str_replace("\\", "\\\\", $escapedSvgContent); // Escape backslashes
            $classContent .= "        '$formattedName' => '$escapedSvgContent',\n";
        }

        // Close the array and class
        $classContent .= "    ];\n\n";

        // Add the method to get the SVG content
        $classContent .= "    public static function getIcon(\$name, \$size = '24', \$color = 'currentColor')\n    {\n";
        $classContent .= "        if (!isset(self::\$icons[\$name])) {\n";
        $classContent .= "            return ''; // Return empty string if icon is not found\n";
        $classContent .= "        }\n\n";
        $classContent .= "        \$iconContent = self::\$icons[\$name];\n\n";
        $classContent .= "        return self::updateSvgAttributes(\$iconContent, \$size, \$color);\n";
        $classContent .= "    }\n\n";

        // Add the updateSvgAttributes method
        $classContent .= "    private static function updateSvgAttributes(\$svgContent, \$size, \$color)\n    {\n";
        $classContent .= "        \$color = preg_match('/^#[0-9A-Fa-f]{6}$/i', \$color) || preg_match('/^[a-zA-Z]+$/', \$color) ? \$color : 'currentColor';\n\n";
        $classContent .= "        \$svgContent = preg_replace('/<svg([^>]*)fill\\s*=\\s*[\"\\'][^\"\\']*[\"\\']([^>]*)>/i', '<svg\$1\$2>', \$svgContent);\n\n";
        $classContent .= "        \$svgContent = preg_replace(\n";
        $classContent .= "            '/<svg([^>]*)>/i',\n";
        $classContent .= "            '<svg\$1 width=\"' . \$size . '\" height=\"' . \$size . '\" fill=\"' . \$color . '\">',\n";
        $classContent .= "            \$svgContent\n";
        $classContent .= "        );\n\n";
        $classContent .= "        \$svgContent = preg_replace(\n";
        $classContent .= "            '/(<path[^>]*)fill\\s*=\\s*[\"\\'][^\"\\']*[\"\\']?/i',\n";
        $classContent .= "            '\$1 fill=\"' . \$color . '\"',\n";
        $classContent .= "            \$svgContent\n";
        $classContent .= "        );\n\n";
        $classContent .= "        return trim(\$svgContent);\n";
        $classContent .= "    }\n";
        $classContent .= "}\n";

        // Write the class content to the output file
        File::put(base_path($outputPath), $classContent);

        $this->info('SVG icon class generated successfully.');
    }
}
