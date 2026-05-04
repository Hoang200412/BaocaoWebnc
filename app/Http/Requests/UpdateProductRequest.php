<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class UpdateProductRequest extends FormRequest
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
        $id = $this->route('product')->id;
        return [
            'category_id' => 'required|exists:categories,id',

            'name'          => 'required|unique:products,name,'.$id,
            'quantity'      => 'required|numeric',
            'price'         => 'required|numeric',
            'description'   => 'required',
            'tags'          => 'required|array',
            'tags.*'        => 'required',
        ];
    }
}