<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Rules\ValidPostalCode;
use App\Rules\ValidSingaporePhoneNumber;
use App\Rules\UserEmailValidation;
use App\Rules\UserPhoneNumberValidation;

class UpdateUserFormRequest extends FormRequest
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
		$user = $this->route('user');

        return [
			'first_name' => ['required', 'string', 'max:255'],
			'last_name' => ['required', 'string', 'max:255'],
			'phone' => ['required', new ValidSingaporePhoneNumber(), new UserPhoneNumberValidation($user->id)],
			'email' => ['required', 'email', 'max:255', new UserEmailValidation($user->id)],
			'postal_code' => ['required', new ValidPostalCode()],
			'address' => ['required'],
			'password' => ['nullable', Rules\Password::defaults(), 'confirmed'],
			'display_name' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
		];
    }

	public function attributes()
	{
		return [
			'phone' => 'contact',
		];
	}

}
