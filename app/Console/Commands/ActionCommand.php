<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ActionCommand extends Command
{
    protected $signature = 'make:action {name}';
    protected $description = 'Create a new action class';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        $parts = explode('/', $name);
        $className = array_pop($parts);
        $subPath = implode('/', $parts);

        $dirPath = app_path('Actions' . ($subPath ? '/' . $subPath : ''));
        $path = $dirPath . '/' . $className . '.php';

        if (!File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }

        $namespace = 'App\\Actions' . ($subPath ? '\\' . implode('\\', $parts) : '');

        // Base itself has nothing to extend.
        $isBase = $namespace === 'App\Actions\Base';
        $import = $isBase ? '' : 'use App\Actions\Base\BaseAction;' . PHP_EOL . PHP_EOL;
        $parent = $isBase ? '' : ' extends BaseAction';

        $stub = <<<EOT
<?php

declare(strict_types=1);

namespace {$namespace};

{$import}class {$className}{$parent}
{
    public function __construct() {
    }

    public function execute() {
    }
}

EOT;

        File::put($path, $stub);
        $this->info("Action {$name} created successfully.");
    }
}
