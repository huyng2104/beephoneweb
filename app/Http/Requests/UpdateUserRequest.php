<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user'))
            ],

            'phone' => ['nullable', 'string', 'regex:/^(84|0[3|5|7|8|9])[0-9]{8}$/'],

            'role' => ['required', 'string'],

            'password' => ['nullable', 'string', 'min:8'],

            'comfirm_password' => ['nullable', 'same:password'],

            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'string'   => ':attribute phải là một chuỗi ký tự.',
            'max'      => [
                'string' => ':attribute không được vượt quá :max ký tự.',
                'file'   => ':attribute không được vượt quá :max kilobytes.',
            ],
            'min'      => [
                'string' => ':attribute phải có ít nhất :min ký tự.',
            ],
            'email'    => ':attribute không đúng định dạng hợp lệ.',
            'unique'   => ':attribute này đã được sử dụng trong hệ thống.',
            'regex'    => ':attribute không đúng định dạng (Ví dụ: 0912345678).',
            'same'     => ':attribute không khớp với mật khẩu đã nhập.',
            'image'    => ':attribute bắt buộc phải là hình ảnh.',
            'mimes'    => ':attribute chỉ chấp nhận các định dạng: :values.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'             => 'Họ và tên',
            'email'            => 'Địa chỉ email',
            'phone'            => 'Số điện thoại',
            'role'             => 'Vai trò',
            'password'         => 'Mật khẩu',
            'comfirm_password' => 'Xác nhận mật khẩu',
            'avatar'           => 'Ảnh đại diện',
        ];
    }
}
