<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'ward_code' => 'required|string',
            'province_name' => 'required|string',
            'district_name' => 'required|string',
            'ward_name' => 'required|string',
            'street_address' => 'required|string',
            'shipping_fee' => 'nullable|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
        ];
    }
}
