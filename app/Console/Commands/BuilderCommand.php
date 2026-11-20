<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BuilderCommand extends Command
{
    protected $signature = 'make:builder {name}';

    protected $description = 'Create a new Eloquent query builder (supports subfolders)';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        $parts = explode('/', $name);
        $className = array_pop($parts);
        $subPath = implode('/', $parts);

        $dirPath = app_path('Builders' . ($subPath ? '/' . $subPath : ''));
        $path = $dirPath . '/' . $className . '.php';

        if (File::exists($path)) {
            $this->error("Builder {$className} already exists.");

            return self::FAILURE;
        }

        if (! File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }

        $namespace = 'App\\Builders' . ($subPath ? '\\' . implode('\\', $parts) : '');
        $model = Str::replaceLast('Builder', '', $className);

        $stub = <<<EOT
        <?php

        namespace {$namespace};

        use App\\Builders\\BaseBuilder;

        class {$className} extends BaseBuilder
        {
            //
        }

        EOT;

        File::put($path, $stub);

        $this->info("Builder {$className} created successfully.");
        $this->line("Wire it up in App\\Models\\{$model}:");
        $this->line("    public function newEloquentBuilder(\$query): {$className}");
        $this->line("    {");
        $this->line("        return new {$className}(\$query);");
        $this->line("    }");

        return self::SUCCESS;
    }
}
