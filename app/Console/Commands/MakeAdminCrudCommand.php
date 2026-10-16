<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
/*
php artisan make:admin-crud — Usage Guide
Scenario 1: Table already exists in DB (e.g., your existing floors, directions)
bash

php artisan make:admin-crud Floor
It reads columns directly from MySQL, detects their types, and generates everything. Skips files that already exist.

Scenario 2: New table (with fields specified inline)
bash

php artisan make:admin-crud Amenity --fields="icon:image,color:string"
Creates migration + all files. Fields you can use:

string → text input
text → textarea
integer / float / decimal → number input
image → dropzone image upload
foreignId → select dropdown (auto-detects relation)
date / datetime → date pickers
boolean → select
json → translatable
Scenario 3: New table (interactive — no --no-interaction)
bash

php artisan make:admin-crud Amenity
It prompts you to add fields one-by-one, then asks if you want to customize form types before generating.

Scenario 4: Overwrite existing files
bash

php artisan make:admin-crud Floor --force
What it auto-generates:
Artifact	Smart Behavior
Migration	Only if table doesn't exist
Model	Extends SiteModel, wires BaseBuilder, sets $fillable, casts, relationships
Controller	Injects ImageHandlerService only if images are present, passes foreign-key selects to views
FormRequest	Translatable names get name.ar/name.en, foreign keys get exists: rule
Views	table shows translated names, images as thumbnails, foreign keys as relation names
Routes	Placed in the correct admin group
Status toggle	Added to AjaxStatusController
After generating, just run php artisan migrate if it was a new table.
 */
class MakeAdminCrudCommand extends Command
{
    protected $signature = 'make:admin-crud
                            {model : The model name in StudlyCase (e.g. Floor, UnitType)}
                            {--fields= : Comma-separated fields for new migration (e.g. "code:string,price:integer,image:image,description:text,region_id:foreignId")}
                            {--force : Overwrite existing files}';

    protected $description = 'Generate a full admin CRUD module (migration, model, controller, request, views, routes, status) by reading table columns or creating a new table';

    // Column types that map to specific form field types
    private array $fieldTypeMap = [
        'string'    => 'text',
        'varchar'   => 'text',
        'char'      => 'text',
        'text'      => 'textarea',
        'mediumtext' => 'textarea',
        'longtext'  => 'textarea',
        'integer'   => 'number',
        'int'       => 'number',
        'bigint'    => 'number',
        'smallint'  => 'number',
        'tinyint'   => 'number',
        'float'     => 'number',
        'double'    => 'number',
        'decimal'   => 'number',
        'boolean'   => 'select',
        'date'      => 'date',
        'datetime'  => 'date-time',
        'timestamp' => 'date-time',
        'json'      => 'json',
        'image'     => 'image',
        'foreignId' => 'select',
    ];

    // Columns to skip in form/table generation
    private array $skipColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];

    private string $modelName;
    private string $modelNameLower;
    private string $tableName;
    private string $viewFolder;
    private array $columns = [];
    private bool $isNewTable = false;

    public function handle(): int
    {
        $this->modelName = Str::studly($this->argument('model'));
        $this->modelNameLower = Str::snake($this->modelName);
        $this->tableName = Str::snake(Str::plural($this->modelName));
        $this->viewFolder = str_replace('_', '_', $this->tableName); // keep snake for view folder

        $this->info("🔍 Generating Admin CRUD for: {$this->modelName}");
        $this->info("   Table: {$this->tableName}");
        $this->newLine();

        // Step 1: Check if table exists, read columns or create migration
        if (Schema::hasTable($this->tableName)) {
            $this->info("✓ Table '{$this->tableName}' exists. Reading columns...");
            $this->columns = $this->getColumnsFromDatabase();
        } else {
            $this->isNewTable = true;
            $fieldsOption = $this->option('fields');

            if (empty($fieldsOption)) {
                // Interactive: ask for fields
                $this->warn("⚠ Table '{$this->tableName}' does not exist.");
                $this->columns = $this->askForFields();
            } else {
                $this->columns = $this->parseFieldsOption($fieldsOption);
            }

            $this->generateMigration();
        }

        if (empty($this->columns)) {
            $this->error('No columns found or specified. Aborting.');
            return 1;
        }

        $this->displayColumnsTable();

        // Allow customization
        if ($this->confirm('Do you want to customize any field types before generating?', false)) {
            $this->customizeFields();
        }

        // Step 2: Generate all files
        $this->generateModel();
        $this->generateFormRequest();
        $this->generateController();
        $this->generateViews();
        $this->registerRoute();
        $this->registerStatusMethod();

        $this->newLine();
        $this->info('🎉 Admin CRUD generated successfully!');
        $this->newLine();
        $this->table(['File', 'Path'], $this->getGeneratedFilesList());

        if ($this->isNewTable) {
            $this->newLine();
            $this->warn('⚠ Don\'t forget to run: php artisan migrate');
        }

        return 0;
    }

    // ─── Column Reading ────────────────────────────────────────────

    private function getColumnsFromDatabase(): array
    {
        $columns = [];
        $dbColumns = Schema::getColumns($this->tableName);

        foreach ($dbColumns as $column) {
            $name = $column['name'];

            if (in_array($name, $this->skipColumns)) {
                continue;
            }

            $type = $this->normalizeDbType($column['type_name'] ?? $column['type'] ?? 'string');
            $nullable = $column['nullable'] ?? false;

            // Detect if it's a JSON/translatable name field
            $formType = $this->resolveFormType($name, $type);

            $columns[] = [
                'name'      => $name,
                'db_type'   => $type,
                'form_type' => $formType,
                'nullable'  => $nullable,
                'is_json'   => ($type === 'json' || $name === 'name'),
                'is_foreign' => Str::endsWith($name, '_id') && $name !== 'order_id',
            ];
        }

        return $columns;
    }

    private function normalizeDbType(string $type): string
    {
        $type = strtolower($type);
        // Remove size info like varchar(255)
        $type = preg_replace('/\(.*\)/', '', $type);
        return trim($type);
    }

    private function resolveFormType(string $name, string $type): string
    {
        // Special cases by column name
        if ($name === 'active') return 'active-select';
        if ($name === 'order_id') return 'number';
        if (Str::contains($name, 'image') || Str::contains($name, 'photo') || Str::contains($name, 'avatar')) return 'image';
        if (Str::contains($name, 'icon')) return 'image';
        if (Str::contains($name, 'master_plan')) return 'image';
        if (Str::endsWith($name, '_id') && $name !== 'order_id') return 'foreign-select';
        if ($name === 'name' || $type === 'json') return 'translatable';
        if (Str::contains($name, 'email')) return 'text';
        if (Str::contains($name, 'phone')) return 'text';
        if (Str::contains($name, 'password')) return 'password';
        if (Str::contains($name, ['latitude', 'longitude', 'lat', 'lng'])) return 'number';
        if (Str::contains($name, 'color')) return 'color';

        return $this->fieldTypeMap[$type] ?? 'text';
    }

    // ─── Interactive Field Input ───────────────────────────────────

    private function askForFields(): array
    {
        $columns = [];

        // Always add name (translatable) and active
        $columns[] = [
            'name' => 'name', 'db_type' => 'string', 'form_type' => 'translatable',
            'nullable' => false, 'is_json' => true, 'is_foreign' => false,
        ];

        $this->info('Default fields: name (translatable), active, order_id');
        $this->info('Add more fields (leave empty to finish):');
        $this->info('  Format: field_name:type');
        $this->info('  Types: string, text, integer, float, decimal, boolean, date, datetime, image, foreignId');
        $this->newLine();

        while (true) {
            $field = $this->ask('Add field (or press Enter to finish)');
            if (empty($field)) break;

            $parts = explode(':', $field);
            $name = trim($parts[0]);
            $type = trim($parts[1] ?? 'string');

            $formType = $this->resolveFormType($name, $type);

            $columns[] = [
                'name'      => $name,
                'db_type'   => $type,
                'form_type' => $formType,
                'nullable'  => in_array($type, ['image', 'text']) || Str::contains($name, 'image'),
                'is_json'   => $type === 'json',
                'is_foreign' => $type === 'foreignId',
            ];
        }

        // Always add active and order_id
        $columns[] = [
            'name' => 'active', 'db_type' => 'tinyint', 'form_type' => 'active-select',
            'nullable' => false, 'is_json' => false, 'is_foreign' => false,
        ];
        $columns[] = [
            'name' => 'order_id', 'db_type' => 'tinyint', 'form_type' => 'number',
            'nullable' => true, 'is_json' => false, 'is_foreign' => false,
        ];

        return $columns;
    }

    private function parseFieldsOption(string $fields): array
    {
        $columns = [];

        // Always add name translatable first
        $columns[] = [
            'name' => 'name', 'db_type' => 'string', 'form_type' => 'translatable',
            'nullable' => false, 'is_json' => true, 'is_foreign' => false,
        ];

        foreach (explode(',', $fields) as $field) {
            $parts = explode(':', trim($field));
            $name = trim($parts[0]);
            $type = trim($parts[1] ?? 'string');

            if ($name === 'name' || $name === 'active' || $name === 'order_id') continue;

            $formType = $this->resolveFormType($name, $type);

            $columns[] = [
                'name'      => $name,
                'db_type'   => $type,
                'form_type' => $formType,
                'nullable'  => in_array($type, ['image', 'text', 'foreignId']) || Str::contains($name, 'image'),
                'is_json'   => $type === 'json',
                'is_foreign' => $type === 'foreignId',
            ];
        }

        // Always add active and order_id
        $columns[] = [
            'name' => 'active', 'db_type' => 'tinyint', 'form_type' => 'active-select',
            'nullable' => false, 'is_json' => false, 'is_foreign' => false,
        ];
        $columns[] = [
            'name' => 'order_id', 'db_type' => 'tinyint', 'form_type' => 'number',
            'nullable' => true, 'is_json' => false, 'is_foreign' => false,
        ];

        return $columns;
    }

    private function displayColumnsTable(): void
    {
        $rows = [];
        foreach ($this->columns as $col) {
            $rows[] = [$col['name'], $col['db_type'], $col['form_type'], $col['nullable'] ? 'Yes' : 'No'];
        }
        $this->table(['Column', 'DB Type', 'Form Type', 'Nullable'], $rows);
    }

    private function customizeFields(): void
    {
        $formTypes = ['text', 'number', 'textarea', 'select', 'active-select', 'foreign-select', 'image', 'date', 'date-time', 'translatable', 'color', 'password', 'hidden'];

        foreach ($this->columns as $i => &$col) {
            $newType = $this->choice(
                "Form type for '{$col['name']}' (current: {$col['form_type']})",
                $formTypes,
                array_search($col['form_type'], $formTypes) ?: 0
            );
            $col['form_type'] = $newType;
        }
    }

    // ─── Migration Generator ───────────────────────────────────────

    private function generateMigration(): void
    {
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_create_{$this->tableName}_table.php";
        $path = database_path("migrations/{$filename}");

        $schemaLines = [];
        $schemaLines[] = '            $table->id();';

        foreach ($this->columns as $col) {
            $schemaLines[] = $this->buildMigrationLine($col);
        }

        $schemaLines[] = '            $table->timestamps();';

        $schema = implode("\n", $schemaLines);

        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$this->tableName}', function (Blueprint \$table) {
{$schema}
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$this->tableName}');
    }
};

PHP;

        $this->writeFile($path, $content);
        $this->info("✓ Migration created: {$filename}");
    }

    private function buildMigrationLine(array $col): string
    {
        $name = $col['name'];
        $type = $col['db_type'];
        $nullable = $col['nullable'];

        return match (true) {
            $name === 'active' => "            \$table->tinyInteger('active')->default(1);",
            $name === 'order_id' => "            \$table->tinyInteger('order_id')->nullable();",
            $name === 'name' || $col['is_json'] => "            \$table->string('{$name}');",
            $type === 'foreignId' => "            \$table->foreignId('{$name}')->nullable()->constrained()->nullOnDelete();",
            $type === 'image' => "            \$table->string('{$name}')->nullable();",
            $type === 'text' || $type === 'mediumtext' || $type === 'longtext' => "            \$table->text('{$name}')" . ($nullable ? '->nullable()' : '') . ';',
            $type === 'integer' || $type === 'int' => "            \$table->integer('{$name}')" . ($nullable ? '->nullable()' : '->default(0)') . ';',
            $type === 'bigint' => "            \$table->bigInteger('{$name}')" . ($nullable ? '->nullable()' : '->default(0)') . ';',
            $type === 'float' => "            \$table->float('{$name}')" . ($nullable ? '->nullable()' : '->default(0)') . ';',
            $type === 'double' => "            \$table->double('{$name}')" . ($nullable ? '->nullable()' : '->default(0)') . ';',
            $type === 'decimal' => "            \$table->decimal('{$name}', 10, 2)" . ($nullable ? '->nullable()' : '->default(0)') . ';',
            $type === 'boolean' => "            \$table->boolean('{$name}')->default(false);",
            $type === 'date' => "            \$table->date('{$name}')" . ($nullable ? '->nullable()' : '') . ';',
            $type === 'datetime' || $type === 'timestamp' => "            \$table->datetime('{$name}')" . ($nullable ? '->nullable()' : '') . ';',
            default => "            \$table->string('{$name}')" . ($nullable ? '->nullable()' : '') . ';',
        };
    }

    // ─── Model Generator ───────────────────────────────────────────

    private function generateModel(): void
    {
        $path = app_path("Models/{$this->modelName}.php");

        if (File::exists($path) && !$this->option('force')) {
            $this->warn("⏭ Model already exists: {$this->modelName}. Skipping (use --force to overwrite).");
            return;
        }

        $fillable = collect($this->columns)->pluck('name')->map(fn($n) => "        '{$n}'")->implode(",\n");

        $casts = [];
        foreach ($this->columns as $col) {
            if ($col['name'] === 'name' || $col['is_json']) {
                $casts[] = "        '{$col['name']}' => 'array'";
            }
        }
        $castsStr = empty($casts) ? '' : implode(",\n", $casts) . ",\n    ";

        // Build relationships for foreign keys
        $relationships = '';
        foreach ($this->columns as $col) {
            if ($col['is_foreign']) {
                $relName = Str::camel(str_replace('_id', '', $col['name']));
                $relModel = Str::studly(str_replace('_id', '', $col['name']));
                $relationships .= <<<PHP

    public function {$relName}(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return \$this->belongsTo({$relModel}::class);
    }

PHP;
            }
        }

        $searchable = collect($this->columns)
            ->filter(fn($c) => in_array($c['form_type'], ['translatable', 'text']) && $c['name'] !== 'active')
            ->pluck('name')
            ->map(fn($n) => "'{$n}'")
            ->implode(', ');

        $content = <<<PHP
<?php

namespace App\Models;

use App\Builders\Base\BaseBuilder;

class {$this->modelName} extends SiteModel
{
    public array \$searchable = [{$searchable}];

    public function newEloquentBuilder(\$query): BaseBuilder
    {
        return new BaseBuilder(\$query);
    }

    protected \$fillable = [
{$fillable}
    ];

    protected function casts(): array
    {
        return [
    {$castsStr}];
    }
{$relationships}}

PHP;

        $this->writeFile($path, $content);
        $this->info("✓ Model created: {$this->modelName}");
    }

    // ─── FormRequest Generator ─────────────────────────────────────

    private function generateFormRequest(): void
    {
        $requestName = "{$this->modelName}Request";
        $dir = app_path("Http/Requests/Admin");
        $path = "{$dir}/{$requestName}.php";

        if (File::exists($path) && !$this->option('force')) {
            $this->warn("⏭ Request already exists: {$requestName}. Skipping.");
            return;
        }

        File::ensureDirectoryExists($dir);

        $rules = [];
        foreach ($this->columns as $col) {
            $rule = $this->buildValidationRule($col);
            if ($rule) {
                $rules[] = $rule;
            }
        }
        $rulesStr = implode("\n", $rules);

        $content = <<<PHP
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class {$requestName} extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
{$rulesStr}
        ];
    }

    public function messages(): array
    {
        return [
            'name.ar.required' => __('The Arabic name is required'),
            'name.en.required' => __('The English name is required'),
        ];
    }
}

PHP;

        $this->writeFile($path, $content);
        $this->info("✓ FormRequest created: {$requestName}");
    }

    private function buildValidationRule(array $col): ?string
    {
        $name = $col['name'];
        $formType = $col['form_type'];

        if ($name === 'order_id') {
            return "            'order_id' => 'nullable|integer|min:0',";
        }

        return match ($formType) {
            'translatable' => "            '{$name}.ar' => 'required|string|max:255',\n            '{$name}.en' => 'required|string|max:255',",
            'active-select' => "            'active' => 'required|in:0,1',",
            'text' => "            '{$name}' => '" . ($col['nullable'] ? 'nullable' : 'required') . "|string|max:255',",
            'textarea' => "            '{$name}' => '" . ($col['nullable'] ? 'nullable' : 'required') . "|string',",
            'number' => $name !== 'order_id' ? "            '{$name}' => '" . ($col['nullable'] ? 'nullable' : 'required') . "|numeric'," : null,
            'image' => "            '{$name}' => 'nullable|string',",
            'date' => "            '{$name}' => '" . ($col['nullable'] ? 'nullable' : 'required') . "|date',",
            'date-time' => "            '{$name}' => '" . ($col['nullable'] ? 'nullable' : 'required') . "|date',",
            'foreign-select' => "            '{$name}' => 'nullable|integer|exists:" . Str::plural(str_replace('_id', '', $name)) . ",id',",
            'select' => "            '{$name}' => '" . ($col['nullable'] ? 'nullable' : 'required') . "|string',",
            'color' => "            '{$name}' => 'nullable|string|max:20',",
            'password' => "            '{$name}' => 'nullable|string|min:6',",
            default => "            '{$name}' => 'nullable|string|max:255',",
        };
    }

    // ─── Controller Generator ──────────────────────────────────────

    private function generateController(): void
    {
        $controllerName = "{$this->modelName}Controller";
        $requestName = "{$this->modelName}Request";
        $dir = app_path("Http/Controllers/Admin");
        $path = "{$dir}/{$controllerName}.php";

        if (File::exists($path) && !$this->option('force')) {
            $this->warn("⏭ Controller already exists: {$controllerName}. Skipping.");
            return;
        }

        // Determine if we need ImageHandlerService
        $hasImages = collect($this->columns)->contains(fn($c) => $c['form_type'] === 'image');
        // Determine foreign key relationships for create/edit
        $foreignKeys = collect($this->columns)->filter(fn($c) => $c['is_foreign']);

        $useStatements = "use App\\Models\\{$this->modelName};\nuse App\\Http\\Requests\\Admin\\{$requestName};\nuse Illuminate\\Http\\Request;";
        $constructorBody = '';
        $constructorParams = '';

        if ($hasImages) {
            $useStatements .= "\nuse App\\Services\\ImageHandlerService;";
            $constructorParams = 'protected ImageHandlerService $imageHandlerService';
        }

        // Build foreign key model imports and select data
        $foreignImports = '';
        $foreignVarsCreate = '';
        $foreignVarsEdit = '';
        $foreignCompact = '';
        foreach ($foreignKeys as $col) {
            $relModel = Str::studly(str_replace('_id', '', $col['name']));
            $varName = Str::camel(Str::plural(str_replace('_id', '', $col['name'])));
            $foreignImports .= "use App\\Models\\{$relModel};\n";
            $foreignVarsCreate .= "        \${$varName} = {$relModel}::listForSelect('none');\n";
            $foreignVarsEdit .= "        \${$varName} = {$relModel}::listForSelect('none');\n";
            $foreignCompact .= ", '{$varName}'";
        }

        if ($foreignImports) {
            $useStatements .= "\n" . rtrim($foreignImports);
        }

        // Build image handling in store/update
        $imageHandlingStore = '';
        $imageHandlingUpdate = '';
        foreach ($this->columns as $col) {
            if ($col['form_type'] === 'image') {
                $field = $col['name'];
                $imageHandlingStore .= "        \$input['{$field}'] = \$this->imageHandlerService->handleImage(\$request, (object) ['{$field}' => null], 'uploads/{$this->tableName}', '{$field}');\n";
                $imageHandlingUpdate .= "        \$input['{$field}'] = \$this->imageHandlerService->handleImage(\$request, \${$this->modelNameLower}, 'uploads/{$this->tableName}', '{$field}');\n";
            }
        }

        $varName = $this->modelNameLower;
        $viewPath = "admin.{$this->viewFolder}";

        $content = <<<PHP
<?php

namespace App\Http\Controllers\Admin;

{$useStatements}

class {$controllerName} extends AdminController
{
    private const FILTERS = ['search', 'active'];

    public function __construct({$constructorParams})
    {
        parent::__construct();
        \$this->setClass('{$this->modelNameLower}');
        \$this->setTable('{$this->tableName}');
    }

    public function index(Request \$request)
    {
        \$perPage = \$this->getLimitView(\$request->input('limit', \$this->limit));

        \$data = {$this->modelName}::query()
            ->filter(\$request->only(self::FILTERS))
            ->defaultOrder()
            ->paginate(\$perPage);

        \$route_create = \$this->checkPerm(\$this->table . '.create');
        \$route_edit   = \$this->checkPerm(\$this->table . '.edit');

        return view('{$viewPath}.index', compact(
            'data',
            'route_create',
            'route_edit',
        ))->with('i', (\$request->input('page', 1) - 1) * \$perPage);
    }

    public function create()
    {
{$foreignVarsCreate}        return view('{$viewPath}.create'{$this->buildCompactString($foreignKeys)});
    }

    public function store({$requestName} \$request)
    {
        \$input = \$request->validated();
{$imageHandlingStore}        {$this->modelName}::create(\$input);

        return redirect()->route('admin.{$this->tableName}.index')
            ->with('success', __('{$this->modelName} created successfully'));
    }

    public function edit(\$id)
    {
        \${$varName} = {$this->modelName}::find(\$id);
        if (empty(\${$varName})) {
            return \$this->pageError();
        }
{$foreignVarsEdit}
        return view('{$viewPath}.edit', compact('{$varName}'{$foreignCompact}));
    }

    public function update({$requestName} \$request, \$id)
    {
        \${$varName} = {$this->modelName}::find(\$id);
        if (empty(\${$varName})) {
            return \$this->pageError();
        }

        \$input = \$request->validated();
{$imageHandlingUpdate}        \${$varName}->update(\$input);

        return redirect()->route('admin.{$this->tableName}.index')
            ->with('success', __('{$this->modelName} updated successfully'));
    }

    public function destroy(\$id)
    {
        \${$varName} = {$this->modelName}::find(\$id);
        if (empty(\${$varName})) {
            return \$this->pageError();
        }
        \${$varName}->delete();

        return redirect()->route('admin.{$this->tableName}.index')
            ->with('success', __('{$this->modelName} deleted successfully'));
    }
}

PHP;

        $this->writeFile($path, $content);
        $this->info("✓ Controller created: {$controllerName}");
    }

    private function buildCompactString($foreignKeys): string
    {
        if ($foreignKeys->isEmpty()) return '';

        $vars = $foreignKeys->map(function ($col) {
            return "'" . Str::camel(Str::plural(str_replace('_id', '', $col['name']))) . "'";
        })->implode(', ');

        return ", compact({$vars})";
    }

    // ─── Views Generator ───────────────────────────────────────────

    private function generateViews(): void
    {
        $viewDir = resource_path("views/admin/{$this->viewFolder}");
        $compDir = "{$viewDir}/components";

        File::ensureDirectoryExists($compDir);

        $this->generateViewIndex($viewDir);
        $this->generateViewCreate($viewDir);
        $this->generateViewEdit($viewDir);
        $this->generateComponentTable($compDir);
        $this->generateComponentForm($compDir);
        $this->generateComponentHeadSearch($compDir);

        $this->info("✓ Views created: resources/views/admin/{$this->viewFolder}/");
    }

    private function generateViewIndex(string $dir): void
    {
        $title = Str::title(str_replace('_', ' ', $this->tableName));
        $createLabel = "Create {$this->modelName}";

        $content = <<<BLADE
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('{$title}') }}
@stop
@section('content')
    @include('admin.{$this->viewFolder}.components.head-search')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('{$title}') }}</h5>
                </div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', [
                        'table' => '{$this->tableName}',
                        'create_label' => __('{$createLabel}'),
                    ])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.{$this->viewFolder}.components.table')
            @include('admin.layouts.table-foot')
            @include('admin.layouts.paginate-all')
        </div>
    </div>
@stop
@section('after_foot')
    @include('admin.layouts.filter')
    @include('admin.layouts.status')
    @include('admin.layouts.delete')
@stop

BLADE;

        $this->writeFile("{$dir}/index.blade.php", $content);
    }

    private function generateViewCreate(string $dir): void
    {
        $title = "Create {$this->modelName}";
        $hasImages = collect($this->columns)->contains(fn($c) => $c['form_type'] === 'image');

        $enctypeParam = $hasImages ? ", 'enctype' => 'multipart/form-data'" : '';
        $mainFilesSection = $hasImages ? <<<BLADE

@section('mainFiles')
    @include('admin.layouts.forms.dropzone.dropzoneJs')
@stop
BLADE : '';

        $content = <<<BLADE
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('{$title}') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => \$previous_url ?? route('admin.{$this->tableName}.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => '{$this->tableName}'{$enctypeParam}])
    @include('admin.{$this->viewFolder}.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop
{$mainFilesSection}

BLADE;

        $this->writeFile("{$dir}/create.blade.php", $content);
    }

    private function generateViewEdit(string $dir): void
    {
        $title = "Edit {$this->modelName}";
        $varName = $this->modelNameLower;
        $hasImages = collect($this->columns)->contains(fn($c) => $c['form_type'] === 'image');

        $enctypeParam = $hasImages ? "\n        'enctype' => 'multipart/form-data'," : '';
        $mainFilesSection = $hasImages ? <<<BLADE

@section('mainFiles')
    @include('admin.layouts.forms.dropzone.dropzoneJs')
@stop
BLADE : '';

        $content = <<<BLADE
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('{$title}') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => \$previous_url ?? route('admin.{$this->tableName}.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => \${$varName},
        'model_id' => \${$varName}->id,
        'table' => \${$varName}->getTable(),{$enctypeParam}
    ])
    @include('admin.{$this->viewFolder}.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop
{$mainFilesSection}

BLADE;

        $this->writeFile("{$dir}/edit.blade.php", $content);
    }

    private function generateComponentTable(string $dir): void
    {
        $varName = $this->modelNameLower;

        // Build table headers and rows
        $headers = '';
        $rows = '';

        foreach ($this->columns as $col) {
            if ($col['form_type'] === 'active-select') {
                $headers .= "        <th>{{ __('Status') }}</th>\n";
                $rows .= <<<BLADE
        <td>
            @include('admin.layouts.tables.status', [
                'active' => \$model->active,
                'id' => \$model->id,
                'route' => route('admin.' . \$model->getTable() . '.status'),
            ])
        </td>

BLADE;
            } elseif ($col['form_type'] === 'translatable') {
                $label = Str::title(str_replace('_', ' ', $col['name']));
                $headers .= "        <th>{{ __('{$label}') }}</th>\n";
                $rows .= "        <td>{{ \$model->{$col['name']}[\$admin_language] ?? '' }}</td>\n";
            } elseif ($col['form_type'] === 'image') {
                $label = Str::title(str_replace('_', ' ', $col['name']));
                $headers .= "        <th>{{ __('{$label}') }}</th>\n";
                $rows .= "        <td>@if(\$model->{$col['name']})<img src=\"{{ \$model->{$col['name']} }}\" width=\"50\">@endif</td>\n";
            } elseif ($col['form_type'] === 'number' && $col['name'] === 'order_id') {
                $headers .= "        <th>{{ __('Order') }}</th>\n";
                $rows .= "        <td>{{ \$model->order_id }}</td>\n";
            } elseif ($col['form_type'] === 'foreign-select') {
                $relName = Str::camel(str_replace('_id', '', $col['name']));
                $label = Str::title(str_replace('_', ' ', str_replace('_id', '', $col['name'])));
                $headers .= "        <th>{{ __('{$label}') }}</th>\n";
                $rows .= "        <td>{{ \$model->{$relName}?->nameLang() ?? '-' }}</td>\n";
            } elseif (in_array($col['form_type'], ['text', 'number'])) {
                $label = Str::title(str_replace('_', ' ', $col['name']));
                $headers .= "        <th>{{ __('{$label}') }}</th>\n";
                $rows .= "        <td>{{ \$model->{$col['name']} ?? '' }}</td>\n";
            }
            // Skip textarea, password, hidden fields in table
        }

        // Always add Settings column
        $headers .= "        <th>{{ __('Setting') }}</th>\n";

        $content = <<<BLADE
<thead>
    <tr>
{$headers}    </tr>
</thead>
@foreach (\$data as \$key => \$model)
    <tr>
{$rows}        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', ['showEdit' => true])
        </td>
    </tr>
@endforeach

BLADE;

        $this->writeFile("{$dir}/table.blade.php", $content);
    }

    private function generateComponentForm(string $dir): void
    {
        $varName = $this->modelNameLower;

        // Determine if we need the translatable name head
        $hasTranslatable = collect($this->columns)->contains(fn($c) => $c['form_type'] === 'translatable');
        $hasImages = collect($this->columns)->contains(fn($c) => $c['form_type'] === 'image');

        $headInclude = '';
        if ($hasTranslatable) {
            $headInclude = "@include('admin.layouts.forms.head', [\n    'show_name' => true,\n])";
        } else {
            $headInclude = "@include('admin.layouts.forms.headnonlang')";
        }

        // Build form fields (skip translatable name - handled by head, skip active - handled separately)
        $fields = '';
        $hasActive = false;

        foreach ($this->columns as $col) {
            if ($col['form_type'] === 'translatable' && $col['name'] === 'name') continue; // handled by head
            if ($col['form_type'] === 'active-select') {
                $hasActive = true;
                continue; // added at the end
            }

            $fields .= $this->buildFormField($col, $varName);
        }

        // Add active select at the end
        if ($hasActive) {
            $fields .= <<<BLADE
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>

BLADE;
        }

        // Add upload script include if there are image fields
        $uploadInclude = $hasImages ? "\n@include('admin.layouts.upload', ['table' => '{$this->tableName}'])" : '';

        $content = <<<BLADE
{$headInclude}
<div class="row g-3 mt-2 mb-4">
{$fields}</div>
@include('admin.layouts.forms.footer')
{$uploadInclude}

BLADE;

        $this->writeFile("{$dir}/form.blade.php", $content);
    }

    private function buildFormField(array $col, string $varName): string
    {
        $name = $col['name'];
        $label = Str::title(str_replace('_', ' ', $name));
        $notReq = $col['nullable'] ? "'not_req' => true," : '';

        return match ($col['form_type']) {
            'text' => <<<BLADE
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => '{$name}',
            'text_value' => isset(\${$varName}) ? \${$varName}->{$name} : null,
            'label_name' => __('{$label}'),
            {$notReq}
        ])
    </div>

BLADE,
            'number' => <<<BLADE
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => '{$name}',
            'number_value' => isset(\${$varName}) ? \${$varName}->{$name} : 0,
            'label_name' => __('{$label}'),
            {$notReq}
        ])
    </div>

BLADE,
            'textarea' => <<<BLADE
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => '{$name}',
            'text_value' => isset(\${$varName}) ? \${$varName}->{$name} : null,
            'label_name' => __('{$label}'),
            {$notReq}
        ])
    </div>

BLADE,
            'image' => <<<BLADE
    <div class="col-sm-12">
        <label class="form-label">{{ __('{$label}') }}</label>
        @include('admin.layouts.forms.dropzone.dropzone', [
            'name' => '{$name}',
            'existingImageUrl' => !empty(\${$varName}->{$name}) ? asset(ltrim(\${$varName}->{$name}, '/')) : null,
        ])
    </div>

BLADE,
            'date' => <<<BLADE
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => '{$name}',
            'date_value' => isset(\${$varName}) ? \${$varName}->{$name} : null,
            'label_name' => __('{$label}'),
            {$notReq}
        ])
    </div>

BLADE,
            'date-time' => <<<BLADE
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => '{$name}',
            'date_value' => isset(\${$varName}) ? \${$varName}->{$name} : null,
            'label_name' => __('{$label}'),
            {$notReq}
        ])
    </div>

BLADE,
            'foreign-select' => $this->buildForeignSelectField($col, $varName, $label),
            'select' => <<<BLADE
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('{$label}'),
            'select_name' => '{$name}',
            'select_value' => isset(\${$varName}) ? \${$varName}->{$name} : null,
            {$notReq}
        ])
    </div>

BLADE,
            'color' => <<<BLADE
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.color', [
            'color_name' => '{$name}',
            'color_value' => isset(\${$varName}) ? \${$varName}->{$name} : null,
            'label_name' => __('{$label}'),
            {$notReq}
        ])
    </div>

BLADE,
            'translatable' => <<<BLADE
    {{-- {$name} is translatable - add to forms.head show_{$name} if needed --}}

BLADE,
            default => <<<BLADE
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => '{$name}',
            'text_value' => isset(\${$varName}) ? \${$varName}->{$name} : null,
            'label_name' => __('{$label}'),
            {$notReq}
        ])
    </div>

BLADE,
        };
    }

    private function buildForeignSelectField(array $col, string $varName, string $label): string
    {
        $name = $col['name'];
        $pluralVar = Str::camel(Str::plural(str_replace('_id', '', $name)));

        return <<<BLADE
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('{$label}'),
            'select_name' => '{$name}',
            'select_value' => isset(\${$varName}) ? \${$varName}->{$name} : null,
            'select_function' => \${$pluralVar} ?? [],
            'not_req' => true,
        ])
    </div>

BLADE;
    }

    private function generateComponentHeadSearch(string $dir): void
    {
        $content = <<<BLADE
@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'active', 'limit'],
])

                                <div class="col-sm-4">
                                    <div class="box">
                                        <div class="box-body">
                                            @include('admin.layouts.forms.fields.text', [
                                                'label_name' => __('Search (Name)'),
                                                'not_req' => true,
                                                'text_name' => 'search',
                                                'text_value' => request()->input('search') ?? null,
                                            ])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="box">
                                        <div class="box-body">
                                            @include('admin.layouts.forms.fields.select', [
                                                'not_req' => true,
                                                'label_name' => __('Status'),
                                                'select_name' => 'active',
                                                'select_value' => request()->input('active') ?? null,
                                                'select_function' => [
                                                    '' => __('All'),
                                                    '1' => __('Active'),
                                                    '0' => __('Inactive'),
                                                ],
                                            ])
                                        </div>
                                    </div>
                                </div>

@include('admin.layouts.components.filter.footer')

BLADE;

        $this->writeFile("{$dir}/head-search.blade.php", $content);
    }

    // ─── Route Registration ────────────────────────────────────────

    private function registerRoute(): void
    {
        $routeFile = base_path('routes/admin.php');

        if (!File::exists($routeFile)) {
            $this->warn("⏭ Route file not found: routes/admin.php");
            return;
        }

        $contents = File::get($routeFile);
        $controllerClass = "App\\Http\\Controllers\\Admin\\{$this->modelName}Controller";
        $controllerShort = "{$this->modelName}Controller";

        // Check if already registered
        if (Str::contains($contents, "Route::resource('{$this->tableName}'")) {
            $this->warn("⏭ Route already registered for '{$this->tableName}'. Skipping.");
            return;
        }

        // Add use statement if not already there
        $useStatement = "use App\\Http\\Controllers\\Admin\\{$controllerShort};";
        if (!Str::contains($contents, $useStatement)) {
            // Insert after the last use statement for Admin controllers
            $lastUsePos = strrpos($contents, "use App\\Http\\Controllers\\Admin\\");
            if ($lastUsePos !== false) {
                $endOfLine = strpos($contents, "\n", $lastUsePos);
                $contents = substr($contents, 0, $endOfLine + 1) . $useStatement . "\n" . substr($contents, $endOfLine + 1);
            }
        }

        // Add the route block before the closing of the main admin group
        // Strategy: Find the "// Image Upload" comment or "clear-cache" route as anchor (last section in main group)
        // Insert before that section
        $statusMethodName = Str::camel($this->modelNameLower) . 'Status';
        $comment = Str::title(str_replace('_', ' ', $this->tableName));

        $routeBlock = <<<PHP

    // {$comment}
    Route::post('{$this->tableName}/status', [AjaxStatusController::class, '{$statusMethodName}'])->name('{$this->tableName}.status');
    Route::resource('{$this->tableName}', {$controllerShort}::class);
PHP;

        // Try to find the "// Image Upload" or "// Theme & Language" comment as insertion anchor
        $anchors = ['    // Image Upload', '    // Theme & Language'];
        $inserted = false;

        foreach ($anchors as $anchor) {
            $anchorPos = strpos($contents, $anchor);
            if ($anchorPos !== false) {
                $contents = substr($contents, 0, $anchorPos) . $routeBlock . "\n\n" . substr($contents, $anchorPos);
                $inserted = true;
                break;
            }
        }

        // Fallback: insert before the closing }); that is followed by Route::group for translations
        if (!$inserted) {
            $translationGroupPos = strpos($contents, "Route::group([\n    'prefix' => 'admin/translations'");
            if ($translationGroupPos === false) {
                $translationGroupPos = strpos($contents, "'prefix' => 'admin/translations'");
            }

            if ($translationGroupPos !== false) {
                // Find the }); just before it
                $beforeTranslation = substr($contents, 0, $translationGroupPos);
                $lastClosing = strrpos($beforeTranslation, '});');
                if ($lastClosing !== false) {
                    $contents = substr($contents, 0, $lastClosing) . $routeBlock . "\n" . substr($contents, $lastClosing);
                    $inserted = true;
                }
            }
        }

        // Last fallback: insert before the very last });
        if (!$inserted) {
            $lastClosing = strrpos($contents, '});');
            if ($lastClosing !== false) {
                $contents = substr($contents, 0, $lastClosing) . $routeBlock . "\n" . substr($contents, $lastClosing);
            }
        }

        File::put($routeFile, $contents);
        $this->info("✓ Routes registered in routes/admin.php");
    }

    // ─── AjaxStatusController Method ──────────────────────────────

    private function registerStatusMethod(): void
    {
        $path = app_path('Http/Controllers/Admin/AjaxStatusController.php');

        if (!File::exists($path)) {
            $this->warn("⏭ AjaxStatusController not found. Skipping status method.");
            return;
        }

        $contents = File::get($path);
        $methodName = Str::camel($this->modelNameLower) . 'Status';

        // Check if method already exists
        if (Str::contains($contents, "function {$methodName}")) {
            $this->warn("⏭ Status method '{$methodName}' already exists. Skipping.");
            return;
        }

        // Add use statement for model if not present
        $useStatement = "use App\\Models\\{$this->modelName};";
        if (!Str::contains($contents, $useStatement)) {
            $lastUsePos = strrpos($contents, "use App\\Models\\");
            if ($lastUsePos !== false) {
                $endOfLine = strpos($contents, "\n", $lastUsePos);
                $contents = substr($contents, 0, $endOfLine + 1) . $useStatement . "\n" . substr($contents, $endOfLine + 1);
            }
        }

        // Add method before the last closing brace
        $method = <<<PHP

    public function {$methodName}(Request \$request)
    {
        \$input = \$request->all();
        \$response = {$this->modelName}::updateActive(\$input['id'], \$input['status']);
        return response()->json(\$response);
    }
PHP;

        // Find last } in the file (class closing brace)
        $lastBrace = strrpos($contents, '}');
        $contents = substr($contents, 0, $lastBrace) . $method . "\n" . substr($contents, $lastBrace);

        File::put($path, $contents);
        $this->info("✓ Status method added to AjaxStatusController: {$methodName}()");
    }

    // ─── Helpers ───────────────────────────────────────────────────

    private function writeFile(string $path, string $content): void
    {
        $dir = dirname($path);
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($path, $content);
    }

    private function getGeneratedFilesList(): array
    {
        $files = [];

        if ($this->isNewTable) {
            $files[] = ['Migration', "database/migrations/*_create_{$this->tableName}_table.php"];
        }
        $files[] = ['Model', "app/Models/{$this->modelName}.php"];
        $files[] = ['FormRequest', "app/Http/Requests/Admin/{$this->modelName}Request.php"];
        $files[] = ['Controller', "app/Http/Controllers/Admin/{$this->modelName}Controller.php"];
        $files[] = ['View: index', "resources/views/admin/{$this->viewFolder}/index.blade.php"];
        $files[] = ['View: create', "resources/views/admin/{$this->viewFolder}/create.blade.php"];
        $files[] = ['View: edit', "resources/views/admin/{$this->viewFolder}/edit.blade.php"];
        $files[] = ['View: table', "resources/views/admin/{$this->viewFolder}/components/table.blade.php"];
        $files[] = ['View: form', "resources/views/admin/{$this->viewFolder}/components/form.blade.php"];
        $files[] = ['View: search', "resources/views/admin/{$this->viewFolder}/components/head-search.blade.php"];
        $files[] = ['Routes', 'routes/admin.php'];
        $files[] = ['Status', 'app/Http/Controllers/Admin/AjaxStatusController.php'];

        return $files;
    }
}
