<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PremiumPayRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'razorpay_id' => 'required|integer|exists:developer_premium_prices,id',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'nullable|string',
            'razorpay_signature' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'razorpay_id.required' => 'The plan selection is required.',
            'razorpay_id.integer' => 'The selected plan is invalid.',
            'razorpay_id.exists' => 'The selected plan does not exist.',

            'razorpay_payment_id.required' => 'Payment ID is required.',
            'razorpay_payment_id.string' => 'Payment ID must be a string.',

            'razorpay_order_id.string' => 'Order ID must be a string.',

            'razorpay_signature.string' => 'Signature must be a string.',
        ];
    }
}
