<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class FormRequest extends Command
{
    protected $signature = 'make:form-request {model : The name of the model}';
    protected $description = 'Generate Form Request classes with validation rules based on model or migration';

    public function handle()
    {
        $modelName = $this->argument('model');
        $modelClass = "App\\Models\\{$modelName}";

        if (!class_exists($modelClass)) {
            $this->error("Model {$modelName} does not exist!");
            return;
        }

        $this->generateFormRequests($modelName);
        $this->info("Form Request classes generated successfully for {$modelName}!");
    }

    protected function generateFormRequests($modelName)
    {
        $model = app("App\\Models\\{$modelName}");
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);

        $rules = $this->generateValidationRules($columns, $table);

        // Generate Store Request
        $this->createFormRequest($modelName, 'Store', $rules['store']);

        // Generate Update Request
        $this->createFormRequest($modelName, 'Update', $rules['update']);

        // Generate List Request
        $this->createFormRequest($modelName, 'List', $this->getListRules());
    }

    protected function generateValidationRules($columns, $table)
    {
        $storeRules = [];
        $updateRules = [];

        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $columnType = Schema::getColumnType($table, $column);
            $rules = [];

            // Basic rules based on column type
            switch ($columnType) {
                case 'string':
                    $rules[] = 'string';
                    $rules[] = 'max:255';
                    break;
                case 'text':
                    $rules[] = 'string';
                    break;
                case 'integer':
                case 'bigint':
                    $rules[] = 'integer';
                    break;
                case 'decimal':
                case 'double':
                    $rules[] = 'numeric';
                    break;
                case 'boolean':
                    $rules[] = 'boolean';
                    break;
                case 'date':
                case 'datetime':
                    $rules[] = 'date';
                    break;
            }

            // Check if nullable using raw DB query
            $isNullable = $this->isColumnNullable($table, $column);

            if ($isNullable) {
                $rules[] = 'nullable';
            } else {
                $storeRules[$column][] = 'required';
            }

            // Check for unique constraint
            if ($this->isUniqueColumn($table, $column)) {
                $storeRules[$column][] = "unique:{$table},{$column}";
                $updateRules[$column][] = "unique:{$table},{$column},{\$this->id}";
            }

            if (!empty($rules)) {
                $storeRules[$column] = array_merge($storeRules[$column] ?? [], $rules);
                $updateRules[$column] = array_merge($updateRules[$column] ?? [], $rules);
            }
        }

        return [
            'store' => $storeRules,
            'update' => $updateRules
        ];
    }

    protected function isColumnNullable($table, $column)
    {
        $database = DB::getDatabaseName();
        $columnInfo = DB::select("
            SELECT is_nullable
            FROM information_schema.columns
            WHERE table_schema = 'public'
            AND table_name = ?
            AND column_name = ?
        ", [$table, $column]);

        return !empty($columnInfo) && $columnInfo[0]->is_nullable === 'YES';
    }

    protected function isUniqueColumn($table, $column)
    {
        $indexes = DB::select("
            SELECT indexname
            FROM pg_indexes
            WHERE tablename = ?
            AND indexdef LIKE ?
        ", [$table, "%{$column}%"]);

        foreach ($indexes as $index) {
            if (stripos($index->indexname, 'unique') !== false) {
                return true;
            }
        }
        return false;
    }

    protected function getListRules()
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'sort_by' => ['nullable', 'string'],
            'sort_direction' => ['nullable', 'in:asc,desc'],
        ];
    }

    protected function createFormRequest($modelName, $type, $rules)
    {
        $className = "{$type}{$modelName}Request";
        $namespace = "App\\Http\\Requests\\{$modelName}";
        $path = app_path("Http/Requests/{$modelName}/{$className}.php");

        $rulesString = $this->formatRules($rules);

        $stub = <<<EOT
<?php

namespace {$namespace};

use Illuminate\Foundation\Http\FormRequest;

class {$className} extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return {$rulesString};
    }
}
EOT;

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $stub);
    }

    protected function formatRules($rules)
    {
        if (empty($rules)) {
            return "[]";
        }

        $rulesArray = [];
        foreach ($rules as $field => $fieldRules) {
            $rulesArray[] = "    \"{$field}\" => [\"" . implode('", "', $fieldRules) . "\"]";
        }

        return "[\n        " . implode(",\n        ", $rulesArray) . "\n        ]";
    }
}
