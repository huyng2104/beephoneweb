<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
            'name'        => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập tên vai trò.',
            'name.unique'   => 'Vai trò này đã được sử dụng.',
            'name.max'      => 'Tên vai trò quá dài (tối đa 50 ký tự).',
            'description.max' => 'Mô tả quá dài (tối đa 255 ký tự).',
        ];
    }
}
