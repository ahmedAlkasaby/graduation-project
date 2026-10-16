<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class HelperCommand extends Command
{
    protected $signature = 'make:helper {name}';
    protected $description = 'Create a new helper class';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        $parts = explode('/', $name);
        $className = array_pop($parts);
        $subPath = implode('/', $parts);

        $dirPath = app_path('Helpers' . ($subPath ? '/' . $subPath : ''));
        $path = $dirPath . '/' . $className . '.php';

        if (!File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }

        $namespace = 'App\\Helpers' . ($subPath ? '\\' . implode('\\', $parts) : '');

        $stub = <<<EOT
<?php


?>

EOT;

        File::put($path, $stub);
        $this->info("Helper {$name} created successfully.");
    }
}
