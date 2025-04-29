<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        "name" => ["required", "unique:categories,name"],
        "slug" => ["required", "unique:categories,slug"],
        "description" => ["nullable"],
        "image" => ["nullable"],
        "parent_id" => ["nullable"],
        "created_by" => ["nullable"],
        "updated_by" => ["nullable"],
        "deleted_by" => ["nullable"],
        "is_active" => ["required"],
        "is_archived" => ["required"]
    ];
    }
}