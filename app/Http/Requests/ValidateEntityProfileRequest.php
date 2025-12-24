<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPostalCode;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;

class ValidateEntityProfileRequest extends FormRequest
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
			'name' => ['required', 'string', 'max:255'],
			'postal_code' => ['required', new ValidPostalCode()],
			'address' => ['required'],
        ];
    }

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(
	        ApiResponse::validation($validator->errors())
	    );
	}
}
