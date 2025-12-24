<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rules;
use App\Rules\ValidPostalCode;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;

class ValidateUserProfileRequest extends FormRequest
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
			'first_name' => ['required', 'string', 'max:255'],
			'last_name' => ['required', 'string', 'max:255'],
			'postal_code' => ['nullable', new ValidPostalCode()],
        ];
    }

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(
			ApiResponse::validation($validator->errors())
		);
	}

	protected function prepareForValidation()
	{
		if ($this->has('phone')) {
			$this->merge([
				'phone' => preg_replace('/\s+/', '', $this->input('phone')),
			]);
		}
	}

}
