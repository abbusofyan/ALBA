<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPostalCode;
use Illuminate\Validation\Rule;

class UpdateBinFormRequest extends FormRequest
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
			'bin_type_id' => ['required'],
			'address' => ['required'],
			'postal_code' => ['required', new ValidPostalCode()],
			'lat' => ['required'],
			'map_radius' => ['required_if:bin_type_id,1'],
			'code' => [
                'required_if:bin_type_id,1',
                Rule::unique('bins', 'code')->ignore($this->route('bin')) // 👈 ignore current bin
            ],
        ];
    }

	public function messages() {
		return [
			'lat.required' => 'Please pin a location on the map',
			'map_radius.required_if' => 'Please select map radius',
			'code.required_if' => 'Please input Bin ID',
			'code.unique' => 'Bin ID is already taken'
		];
	}

	public function attributes()
	{
		return [
			'bin_type_id' => 'Bin type',
			'code' => 'Bin ID'
		];
	}
}
