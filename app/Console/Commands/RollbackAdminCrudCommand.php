<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
/*
# Preview what would be deleted (safe)
php artisan admin-crud:rollback Amenity --dry-run

# Actually rollback
php artisan admin-crud:rollback Amenity

# Keep the migration (if you already ran migrate and don't want to lose the table)
php artisan admin-crud:rollback Amenity --keep-migration

*/
class RollbackAdminCrudCommand extends Command
{
    protected $signature = 'admin-crud:rollback
                            {model : The model name in StudlyCase (e.g. Amenity, Feature)}
                            {--keep-migration : Keep the migration file (useful if already migrated)}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Rollback/undo a generated admin CRUD module — removes all generated files, routes, and status method';

    private string $modelName;
    private string $modelNameLower;
    private string $tableName;
    private string $viewFolder;
    private array $deleted = [];
    private array $skipped = [];
    private bool $dryRun = false;

    public function handle(): int
    {
        $this->modelName = Str::studly($this->argument('model'));
        $this->modelNameLower = Str::snake($this->modelName);
        $this->tableName = Str::snake(Str::plural($this->modelName));
        $this->viewFolder = $this->tableName;
        $this->dryRun = (bool) $this->option('dry-run');

        $prefix = $this->dryRun ? '🔍 [DRY RUN]' : '🗑️';
        $this->info("{$prefix} Rolling back Admin CRUD for: {$this->modelName}");
        $this->info("   Table: {$this->tableName}");
        $this->newLine();

        if (!$this->dryRun && !$this->confirm("This will permanently delete all generated files for '{$this->modelName}'. Continue?", true)) {
            $this->info('Aborted.');
            return 0;
        }

        // 1. Remove Model
        $this->deleteFile(app_path("Models/{$this->modelName}.php"), 'Model');

        // 2. Remove Controller
        $this->deleteFile(
            app_path("Http/Controllers/Admin/{$this->modelName}Controller.php"),
            'Controller'
        );

        // 3. Remove FormRequest
        $this->deleteFile(
            app_path("Http/Requests/Admin/{$this->modelName}Request.php"),
            'FormRequest'
        );

        // 4. Remove Views folder
        $viewDir = resource_path("views/admin/{$this->viewFolder}");
        $this->deleteDirectory($viewDir, 'Views');

        // 5. Remove Migration (unless --keep-migration)
        if (!$this->option('keep-migration')) {
            $this->deleteMigration();
        } else {
            $this->skipped[] = ['Migration', 'Kept (--keep-migration flag)'];
        }

        // 6. Remove route from routes/admin.php
        $this->removeRoute();

        // 7. Remove status method from AjaxStatusController
        $this->removeStatusMethod();

        // 8. Remove use statement from AjaxStatusController
        $this->removeModelImportFromStatusController();

        $this->newLine();

        if (!empty($this->deleted)) {
            $actionLabel = $this->dryRun ? 'Would delete' : 'Deleted';
            $this->table(['Type', 'Path'], $this->deleted);
        }

        if (!empty($this->skipped)) {
            $this->newLine();
            $this->table(['Type', 'Reason'], $this->skipped);
        }

        $this->newLine();
        if ($this->dryRun) {
            $this->info('🔍 Dry run complete. No files were actually deleted.');
        } else {
            $this->info('✓ Rollback complete for: ' . $this->modelName);

            if (!$this->option('keep-migration')) {
                $this->warn('⚠ If the migration was already run, you may need: php artisan migrate:rollback');
            }
        }

        return 0;
    }

    // ─── File Deletion ─────────────────────────────────────────────

    private function deleteFile(string $path, string $label): void
    {
        if (!File::exists($path)) {
            $this->skipped[] = [$label, 'Not found: ' . $this->relativePath($path)];
            return;
        }

        $this->deleted[] = [$label, $this->relativePath($path)];

        if (!$this->dryRun) {
            File::delete($path);
        }
    }

    private function deleteDirectory(string $path, string $label): void
    {
        if (!File::isDirectory($path)) {
            $this->skipped[] = [$label, 'Not found: ' . $this->relativePath($path)];
            return;
        }

        $fileCount = count(File::allFiles($path));
        $this->deleted[] = [$label, $this->relativePath($path) . "/ ({$fileCount} files)"];

        if (!$this->dryRun) {
            File::deleteDirectory($path);
        }
    }

    private function deleteMigration(): void
    {
        $migrationsPath = database_path('migrations');
        $pattern = "*_create_{$this->tableName}_table.php";
        $files = File::glob("{$migrationsPath}/{$pattern}");

        if (empty($files)) {
            $this->skipped[] = ['Migration', "No migration found matching: {$pattern}"];
            return;
        }

        foreach ($files as $file) {
            $this->deleted[] = ['Migration', $this->relativePath($file)];

            if (!$this->dryRun) {
                File::delete($file);
            }
        }
    }

    // ─── Route Removal ─────────────────────────────────────────────

    private function removeRoute(): void
    {
        $routeFile = base_path('routes/admin.php');

        if (!File::exists($routeFile)) {
            $this->skipped[] = ['Routes', 'routes/admin.php not found'];
            return;
        }

        $contents = File::get($routeFile);
        $original = $contents;

        $controllerShort = "{$this->modelName}Controller";
        $statusMethodName = Str::camel($this->modelNameLower) . 'Status';

        // Remove use statement
        $useStatement = "use App\\Http\\Controllers\\Admin\\{$controllerShort};\n";
        $contents = str_replace($useStatement, '', $contents);

        // Remove the route block: comment + status route + resource route
        // Pattern: optional blank lines + comment + status post + resource
        $patterns = [
            // Full block with comment
            "/\n\s*\/\/ " . preg_quote(Str::title(str_replace('_', ' ', $this->tableName)), '/') . "\n"
                . "\s*Route::post\('" . preg_quote($this->tableName, '/') . "\/status'.*?\n"
                . "\s*Route::resource\('" . preg_quote($this->tableName, '/') . "'.*?\n/",
            // Just the two route lines without comment
            "/\s*Route::post\('" . preg_quote($this->tableName, '/') . "\/status'.*?{$statusMethodName}.*?\n/",
            "/\s*Route::resource\('" . preg_quote($this->tableName, '/') . "'.*?{$controllerShort}.*?\n/",
        ];

        foreach ($patterns as $pattern) {
            $contents = preg_replace($pattern, "\n", $contents);
        }

        // Clean up double blank lines left behind
        $contents = preg_replace("/\n{3,}/", "\n\n", $contents);

        if ($contents !== $original) {
            $this->deleted[] = ['Routes', "routes/admin.php (use + resource + status route)"];

            if (!$this->dryRun) {
                File::put($routeFile, $contents);
            }
        } else {
            $this->skipped[] = ['Routes', 'No matching routes found in routes/admin.php'];
        }
    }

    // ─── Status Method Removal ─────────────────────────────────────

    private function removeStatusMethod(): void
    {
        $path = app_path('Http/Controllers/Admin/AjaxStatusController.php');

        if (!File::exists($path)) {
            $this->skipped[] = ['Status Method', 'AjaxStatusController not found'];
            return;
        }

        $contents = File::get($path);
        $original = $contents;

        $methodName = Str::camel($this->modelNameLower) . 'Status';

        // Remove the method block
        $pattern = "/\n\s*public function {$methodName}\(Request \\\$request\)\s*\{[^}]*\}/s";

        $contents = preg_replace($pattern, '', $contents);

        if ($contents !== $original) {
            $this->deleted[] = ['Status Method', "AjaxStatusController::{$methodName}()"];

            if (!$this->dryRun) {
                File::put($path, $contents);
            }
        } else {
            $this->skipped[] = ['Status Method', "Method '{$methodName}' not found in AjaxStatusController"];
        }
    }

    // ─── Model Import Removal from AjaxStatusController ────────────

    private function removeModelImportFromStatusController(): void
    {
        $path = app_path('Http/Controllers/Admin/AjaxStatusController.php');

        if (!File::exists($path)) {
            return;
        }

        $contents = File::get($path);
        $useStatement = "use App\\Models\\{$this->modelName};\n";

        if (Str::contains($contents, $useStatement)) {
            $contents = str_replace($useStatement, '', $contents);

            if (!$this->dryRun) {
                File::put($path, $contents);
            }
        }
    }

    // ─── Helpers ───────────────────────────────────────────────────

    private function relativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }
}
