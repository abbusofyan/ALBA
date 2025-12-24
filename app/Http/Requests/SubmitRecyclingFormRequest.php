<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmitRecyclingFormRequest extends FormRequest
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
		    'bin_id' => 'required|exists:bins,id',
		    'photo' => 'required|file|mimes:jpg,jpeg,png|max:30720',
		];
    }

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(
			ApiResponse::validation($validator->errors())
		);
	}
}
