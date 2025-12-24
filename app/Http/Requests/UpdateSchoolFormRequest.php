<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Rules\ValidPostalCode;
use App\Rules\ValidSingaporePhoneNumber;

class UpdateSchoolFormRequest extends FormRequest
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
		$school = $this->route('school');

        return [
			'name' => ['required', 'string', 'max:255'],
		    'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($school->id)],
			'phone' => ['required', new ValidSingaporePhoneNumber()],
		    'email' => ['required', 'string', 'email', 'max:255'],
			'postal_code' => ['required', new ValidPostalCode()],
			'address' => ['required'],
			'password' => ['nullable', Rules\Password::defaults(), 'confirmed'],
			'secondary_email' => 'array',
			'secondary_email.*' => 'required|email',
		];
    }

	public function messages() {
		return [
			'secondary_email.*.required' => 'Secondary email is required or remove this field'
		];
	}

	public function attributes()
	{
		return [
			'phone' => 'contact',
			'name' => 'school name',
			'secondary_email.*' => 'secondary email'
		];
	}

}
