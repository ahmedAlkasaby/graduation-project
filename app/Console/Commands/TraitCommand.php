<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TraitCommand extends Command
{
    protected $signature = 'make:trait {name}';
    protected $description = 'Create a new Trait class';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        $parts = explode('/', $name);
        $className = array_pop($parts);
        $subPath = implode('/', $parts);

        $dirPath = app_path('Traits' . ($subPath ? '/' . $subPath : ''));
        $path = $dirPath . '/' . $className . '.php';

        if (!File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }

        $namespace = 'App\\Traits' . ($subPath ? '\\' . implode('\\', $parts) : '');

        $stub = <<<EOT
<?php

namespace {$namespace};

trait {$className}
{
    public function __construct() {
    }

    public function execute() {
    }
}

EOT;

        File::put($path, $stub);
        $this->info("Trait {$name} created successfully.");
    }
}
