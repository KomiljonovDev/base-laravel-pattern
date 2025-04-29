<?php

namespace App\Http\Requests\MetaResource;

use Illuminate\Foundation\Http\FormRequest;

class StoreMetaResourceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'unique:meta_resources,name'],
            'user_id' => ['required']
        ];
    }
}