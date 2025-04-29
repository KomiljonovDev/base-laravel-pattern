<?php

namespace App\Http\Requests\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
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
        $companyId = $this->route('company')->id ?? $this->route('company');
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('companies','name')->ignore($companyId)],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('companies','email')->ignore($companyId)],
            'web_site' => ['nullable', 'string', 'url', 'max:255', Rule::unique('companies','web_site')->ignore($companyId)],
            'phone' => ['required', 'string', 'max:12', Rule::unique('companies','phone')->ignore($companyId)],
        ];
    }
}
