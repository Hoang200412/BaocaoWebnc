<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
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
            'name' => 'required|unique:Categories|max:255',
        ];


    }

   
   public function messages(): array
   {
        return [
            'name.required' => 'Vui lòng nhập đầy đủ thông tin',
            'name.unique'   => 'Tên danh mục này đã tồn tại',
        ];
   }
}
