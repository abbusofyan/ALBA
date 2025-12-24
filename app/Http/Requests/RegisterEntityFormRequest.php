<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidSingaporePhoneNumber;
use App\Rules\ValidPostalCode;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;

class RegisterEntityFormRequest extends FormRequest
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
			'phone' => ['required', new ValidSingaporePhoneNumber()],
			'email' => ['required', 'string', 'email', 'max:255'],
			'password' => ['required', Rules\Password::defaults(), 'confirmed'],
			'postal_code' => ['required', new ValidPostalCode()],
			'address' => ['required'],
			'secondary_email' => 'array',
		    'secondary_email.*' => 'required|email',
        ];
    }

	public function attributes()
	{
		return [
			'phone' => 'contact',
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
