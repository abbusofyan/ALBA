<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;

class ValidatePhoneNumber extends FormRequest
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
			'phone' => [
				'required',
				'numeric',
				'digits:8',
				function ($attribute, $value, $fail) {
					$fullPhone = '+65' . $value;

					$usersWithPhone = User::where('phone', $fullPhone)->get();

					$allowedRoles = ['School', 'Enterprise'];

					if ($usersWithPhone->isNotEmpty()) {
						$invalidUser = $usersWithPhone->first(function ($user) use ($allowedRoles) {
							return !$user->hasAnyRole($allowedRoles);
						});

						if ($invalidUser) {
							$fail('The phone number has already been taken.');
						}
					}
				},
			],
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
}
