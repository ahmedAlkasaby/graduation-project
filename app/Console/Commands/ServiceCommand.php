<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        $parts = explode('/', $name);
        $className = array_pop($parts);
        $subPath = implode('/', $parts);

        $dirPath = app_path('Services' . ($subPath ? '/' . $subPath : ''));
        $path = $dirPath . '/' . $className . '.php';

        if (!File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }

        $namespace = 'App\\Services' . ($subPath ? '\\' . implode('\\', $parts) : '');

        $stub = <<<EOT
<?php

namespace {$namespace};

class {$className}
{
    public function __construct() {
    }

    public function execute() {
    }
}

EOT;

        File::put($path, $stub);
        $this->info("Service {$name} created successfully.");
    }
}
