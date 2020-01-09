<?php

namespace LaravelEnso\DataImport\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaravelEnso\DataImport\App\Contracts\Importable;
use LaravelEnso\DataImport\App\Models\DataImport;
use LaravelEnso\DataImport\App\Services\Validators\Template as Validator;
use LaravelEnso\DataImport\App\Services\Validators\Validator as CustomValidator;
use LaravelEnso\Helpers\App\Classes\JsonParser;
use LaravelEnso\Helpers\App\Classes\Obj;

class Template
{
    private static $rules;

    private Obj $template;
    private array $chunkSizes;

    public function __construct(DataImport $dataImport)
    {
        $this->template = $this->template($dataImport);
        $this->chunkSizes = [];

        if ($this->shouldValidate()) {
            $this->validate();
        }
    }

    public function timeout(): int
    {
        return $this->template->has('timeout')
            ? $this->template->get('timeout')
            : (int) config('enso.imports.timeout');
    }

    public function queue(): string
    {
        return $this->template->has('queue')
            ? $this->template->get('queue')
            : config('enso.imports.queues.processing');
    }

    public function sheetNames(): Collection
    {
        return $this->sheets()->pluck('name');
    }

    public function header(string $sheetName): Collection
    {
        return $this->columns($sheetName)->pluck('name');
    }

    public function validationRules(string $sheetName): array
    {
        return self::$rules ??= $this->columns($sheetName)
            ->filter(fn ($column) => $column->has('validations'))
            ->mapWithKeys(fn ($column) => [
                $column->get('name') => $column->get('validations'),
            ])->toArray();
    }

    public function chunkSize($sheetName): int
    {
        return $this->chunkSizes[$sheetName]
            ??= $this->sheet($sheetName)->has('chunkSize')
                ? $this->sheet($sheetName)->get('chunkSize')
                : (int) config('enso.imports.chunkSize');
    }

    public function importer($sheetName): Importable
    {
        $class = $this->sheet($sheetName)->get('importerClass');

        return new $class();
    }

    public function customValidator($sheetName, $user, $params): ?CustomValidator
    {
        if ($this->sheet($sheetName)->has('validatorClass')) {
            $class = $this->sheet($sheetName)->get('validatorClass');

            return new $class($this->validationRules($sheetName), $user, $params);
        }

        return null;
    }

    private function columns(string $sheetName): Obj
    {
        return $this->sheet($sheetName)->get('columns');
    }

    private function sheet(string $sheetName): Obj
    {
        return $this->sheets()
            ->first(fn ($sheet) => $sheet->get('name') === $sheetName);
    }

    private function sheets(): Obj
    {
        return $this->template->get('sheets');
    }

    private function validate(): void
    {
        (new Validator($this->template))->run();
    }

    private function shouldValidate(): bool
    {
        return in_array(
            config('enso.imports.validations'),
            [App::environment(), 'always']
        );
    }

    private function template(DataImport $dataImport): Obj
    {
        $templatePath = base_path(
            config("enso.imports.configs.{$dataImport->type}.template")
        );

        return new Obj(
            (new JsonParser($templatePath))->array()
        );
    }
}
