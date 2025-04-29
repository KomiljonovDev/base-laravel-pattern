<?php

namespace App\Http\Requests\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>'nullable|string',
            'address'=>'nullable|string',
            'email'=>'nullable|email',
            'web_site'=>'nullable|url',
            'sort_by'=>'nullable|string|in:name,id,updated_at',
            'order_by'=>'nullable|string|in:asc,desc',
            'per_page'=>['nullable','integer','min:1','max:100']
        ];
    }

    public function validated($key = null, $default = null)
    {
        $filters = [
            'name' => ['type' => 'string', 'value' => $this->input('name')],
            'address' => ['type' => 'string', 'value' => $this->input('address')],
            'email' => ['type' => 'string', 'value' => $this->input('email')],
            'web_site' => ['type' => 'string', 'value' => $this->input('web_site')],
            'sort_by' => $this->input('sort_by'),
            'order_by' => $this->input('order_by'),
            'per_page' => $this->input('per_page'),
        ];

        if ($key) {
            return $filters[$key]['value'] ?? $default;
        }

        return $filters;
    }
}
