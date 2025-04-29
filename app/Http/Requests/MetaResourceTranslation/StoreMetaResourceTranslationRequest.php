<?php

namespace App\Http\Requests\MetaResourceTranslation;

use Illuminate\Foundation\Http\FormRequest;

class StoreMetaResourceTranslationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "meta_resource_id" => ["required", "unique:meta_resource_translations,meta_resource_id"],
            "locale" => ["required", "unique:meta_resource_translations,locale"],
            "title" => ["required"],
            "content" => ["required", "string"]
        ];
    }
}