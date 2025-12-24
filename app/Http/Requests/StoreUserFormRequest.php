<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use App\Rules\ValidSingaporePhoneNumber;
use App\Rules\ValidPostalCode;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use App\Rules\UserEmailValidation;
use App\Rules\UserPhoneNumberValidation;
use App\Helpers\ProfanityDetector;
use Illuminate\Validation\Validator as LaravelValidator;

class StoreUserFormRequest extends FormRequest
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
			'email' => ['required', 'email', 'max:255', new UserEmailValidation()],
			'phone' => ['required', 'string', new ValidSingaporePhoneNumber(), new UserPhoneNumberValidation()],
			'postal_code' => ['nullable', new ValidPostalCode()],
			'password' => ['required', Rules\Password::defaults(), 'confirmed'],
			'display_name' => ['nullable', 'string', 'max:255', Rule::unique('users')],
        ];
    }

	public function attributes()
	{
	    return [
	        'phone' => 'phone number',
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

	protected function withValidator(LaravelValidator $validator)
    {
        $validator->after(function ($validator) {
            $displayName = $this->input('display_name');

            if ($displayName) {
                $customBadWords = []; // Or load from config / database / dictionary
                $profanityDetector = new ProfanityDetector($customBadWords);

                if ($profanityDetector->check($displayName)) {
                    $validator->errors()->add('display_name', 'Nickname contains inappropriate language.');
                }
            }
        });
    }

}
