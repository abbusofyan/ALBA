<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRewardRequest extends FormRequest
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
			'name'        => ['required', 'string', 'max:255'],
			'price'       => ['required', 'min:0.1'],
			'label'       => ['required', 'string', 'max:255'],
			'description' => ['required', 'string'],
			'tnc'         => ['required', 'string'],
			'status'      => ['required', 'in:0,1'],
			'image'       => ['nullable', 'image', 'max:2048'],
			'vouchers' 	  => 'required_without:import|array',
			'vouchers.*.code' => [
				'required',
				// 'distinct',
				'string',
				'max:255',
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
			'vouchers.required' 		=> 'Add at least one voucher code.',
			'vouchers.array' 			=> 'The vouchers must be an array.',
			'vouchers.*.code.required'  => 'The voucher code is required.',
			'vouchers.*.code.string' 	=> 'The voucher code must be a string.',
			'vouchers.*.code.max' 		=> 'The voucher code may not be greater than 255 characters.',
			'vouchers.required_without' => 'The vouchers field is required unless you provide an import file.',
		];
	}
}
