<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBinTypeRequest extends FormRequest
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
		return [
			'name' => ['required', 'unique:bin_types,name'],
			'fixed_qrcode' => ['required'],
			'bin_type_waste' => ['required'],
			'point' => ['required', 'numeric'],
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
		];
	}

	public function messages()
	{
		return [
			'fixed_qrcode.required' => 'Please select QR Code Type',
			'bin_type_waste.required' => 'Please put at least one accepted recyclables',
		];
	}

	public function attributes()
	{
		return [
			'name' => 'Bin type name',
			'fixed_qrcode' => 'QR Code Type',
			'bin_type_waste' => 'Accepted Recyclables',
			'point' => 'CO2 Point'
		];
	}
}
