<?php

namespace App\Http\Requests\MetaResourceTranslation;

use Illuminate\Foundation\Http\FormRequest;

class ListMetaResourceTranslationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "per_page" => ["nullable", "integer", "min:1", "max:100"],
            "page" => ["nullable", "integer", "min:1"],
            "sort_by" => ["nullable", "string"],
            "sort_direction" => ["nullable", "in:asc,desc"]
        ];
    }
}