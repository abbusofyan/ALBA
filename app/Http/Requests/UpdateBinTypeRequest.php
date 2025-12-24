<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBinTypeRequest extends FormRequest
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
			'name' => ['required', Rule::unique('bin_types')->ignore($this->bin_type)],
			'fixed_qrcode' => ['required'],
			'bin_type_waste' => ['required'],
			'point' => ['required', 'numeric'],
			'image' => 'required',
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
