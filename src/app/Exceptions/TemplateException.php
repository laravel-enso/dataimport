<?php

namespace LaravelEnso\DataImport\app\Exceptions;

use LaravelEnso\Helpers\app\Classes\Obj;
use LaravelEnso\DataImport\app\Contracts\Importable;
use LaravelEnso\Helpers\app\Exceptions\EnsoException;

class TemplateException extends EnsoException
{
    public static function missingRootAttributes($attrs)
    {
        throw new static(__(
            'Attribute(s) Missing in template: ":attrs"',
            ['attrs' => $attrs]
        ));
    }

    public static function missingSheetAttributes($attrs)
    {
        throw new static(__(
            'Mandatory Attribute(s) Missing in sheet object: ":attrs"',
            ['attrs' => $attrs]
        ));
    }

    public static function unknownSheetAttributes($attrs)
    {
        throw new static(__(
            'Unknown Optional Attribute(s) in sheet object: ":attr"',
            ['attrs' => $attrs]
        ));
    }

    public static function missingColumnAttributes($attrs)
    {
        throw new static(__(
            'Mandatory Attribute(s) Missing in column object: ":attr"',
            ['attrs' => $attrs]
        ));
    }

    public static function unknownColumnAttributes($attrs)
    {
        throw new static(__(
            'Unknown Attribute(s) found in column object: ":attr"',
            ['attrs' => $attrs]
        ));
    }

    public static function missingImporterClass(Obj $sheet)
    {
        throw new static(__(
            'Importer class ":class" for sheet ":sheet" does not exist',
            ['class' => $sheet->get('validatorClass'), 'sheet' => $sheet->get('name')]
        ));
    }

    public static function importerMissingContract(Obj $sheet)
    {
        throw new static(__(
            'Importer class ":class" for sheet ":sheet" must implement the ":contract" contract',
            ['class' => $sheet->get('importerClass'), 'contract' => Importable::class]
        ));
    }

    public static function missingValidatorClass(Obj $sheet)
    {
        throw new static(__(
            'Validator class ":class" for sheet ":sheet" does not exist',
            ['class' => $sheet->get('validatorClass'), 'sheet' => $sheet->get('name')]
        ));
    }

    public static function incorectValidator(Obj $sheet)
    {
        throw new static(__(
            'Validator class ":class" for sheet ":sheet" must extend "LaravelEnso\DataImport\app\Services\Validators\Validator" class',
            ['class' => $sheet->get('validatorClass'), 'sheet' => $sheet->get('name')]
        ));
    }
}
