<?php

namespace Modules\Purchase\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {

        return [
            'vendor_id' => 'required',
            'purchase_date' => 'required|before_or_equal:expected_date',
            'expected_date' => 'required|after_or_equal:purchase_date',
            'exchange_rate' => 'required'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

}
