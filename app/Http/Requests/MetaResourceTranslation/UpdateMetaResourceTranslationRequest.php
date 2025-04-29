<?php

namespace App\Http\Requests\MetaResourceTranslation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMetaResourceTranslationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "meta_resource_id" => ["unique:meta_resource_translations,meta_resource_id,{$this->id}"],
            "locale" => ["unique:meta_resource_translations,locale,{$this->id}"],
            "content" => ["string"]
        ];
    }
}