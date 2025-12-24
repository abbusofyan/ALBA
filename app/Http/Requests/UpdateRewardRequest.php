<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRewardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rewardId = $this->route('reward')->id;

        return [
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'string', 'max:255'],
            'label'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'tnc'         => ['required', 'string'],
            'status'      => ['required', 'in:0,1'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'vouchers'    => ['sometimes', 'array'],
            'vouchers.*.code' => [
                'required',
                'distinct',
                'string',
                'max:255',
                Rule::unique('vouchers', 'code')->where(function ($query) use ($rewardId) {
                    return $query->where('reward_id', $rewardId);
                })->ignore($this->input('vouchers.*.id')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'The name is required.',
            'price.required'       => 'The Required CO2 Point is required.',
            'label.required'       => 'The label is required.',
            'description.required' => 'The description is required.',
            'tnc.required'         => 'The terms and conditions are required.',
            'status.in'            => 'The status must be 0 (inactive) or 1 (active).',
            'image.image'          => 'The file must be a valid image.',
            'image.max'            => 'The image may not be greater than 2MB.',
            'vouchers.*.code.distinct' => 'Each voucher code must be unique.',
            'vouchers.array'             => 'The vouchers must be an array.',
            'vouchers.*.code.required'  => 'The voucher code is required.',
            'vouchers.*.code.string'     => 'The voucher code must be a string.',
            'vouchers.*.code.max'         => 'The voucher code may not be greater than 255 characters.',
            'vouchers.*.code.unique'   => 'The voucher code has already been taken for this reward.',
        ];
    }
}
