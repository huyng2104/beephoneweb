<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucherRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:vouchers,code,' .$this->route('voucher'),
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'boolean',
            'points_required' => 'numeric'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên voucher không được để trống.',
            'name.max' => 'Tên voucher tối đa 255 ký tự.',
            'code.required' => 'Mã voucher không được để trống.',
            'code.unique' => 'Mã voucher đã tồn tại.',
            'code.max' => 'Mã voucher tối đa 50 ký tự.',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.required' => 'Giá trị giảm không được để trống.',
            'discount_value.numeric' => 'Giá trị giảm phải là số.',
            'discount_value.min' => 'Giá trị giảm phải lớn hơn hoặc bằng 0.',
            'max_discount.numeric' => 'Giảm tối đa phải là số.',
            'max_discount.min' => 'Giảm tối đa phải lớn hơn hoặc bằng 0.',
            'min_order_value.numeric' => 'Giá trị đơn tối thiểu phải là số.',
            'min_order_value.min' => 'Giá trị đơn tối thiểu phải lớn hơn hoặc bằng 0.',
            'usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên.',
            'usage_limit.min' => 'Giới hạn sử dụng tối thiểu là 1.',
            'usage_limit_per_user.integer' => 'Giới hạn mỗi người phải là số nguyên.',
            'usage_limit_per_user.min' => 'Giới hạn mỗi người tối thiểu là 1.',
            'start_date.required' => 'Ngày bắt đầu không được để trống.',
            'start_date.date' => 'Ngày bắt đầu không đúng định dạng.',
            'end_date.date' => 'Ngày kết thúc không đúng định dạng.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
        ];
    }
}
