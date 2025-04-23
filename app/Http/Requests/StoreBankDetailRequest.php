<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'branch_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|numeric|digits_between:8,20',
            'ifsc_code' => 'required|string|max:20',
            'micr_number' => 'nullable|string|max:20',
            'account_type' => 'required|in:saving,current', // or add more types if needed
        ];
    }

    public function messages(): array
    {
        return [
            'account_type.in' => 'Account type must be either Saving or Current.',
        ];
    }

}
