<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        "name" => ["unique:categories,name,{$this->route('id')}"],
        "slug" => ["unique:categories,slug,{$this->route('id')}"],
        "description" => ["nullable"],
        "image" => ["nullable"],
        "parent_id" => ["nullable"],
        "created_by" => ["nullable"],
        "updated_by" => ["nullable"],
        "deleted_by" => ["nullable"]
    ];
    }
}