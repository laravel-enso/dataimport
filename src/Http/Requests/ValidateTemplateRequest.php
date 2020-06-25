<?php

namespace LaravelEnso\DataImport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use LaravelEnso\DataImport\Enums\ImportTypes;

class ValidateTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'template' => 'required|file',
            'type' => 'string|in:'.ImportTypes::keys()->implode(','),
        ];
    }
}