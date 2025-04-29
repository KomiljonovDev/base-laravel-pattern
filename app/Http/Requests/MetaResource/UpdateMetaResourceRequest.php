<?php

namespace App\Http\Requests\MetaResource;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMetaResourceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['unique:meta_resources,name,{$this->id}']
        ];
    }
}