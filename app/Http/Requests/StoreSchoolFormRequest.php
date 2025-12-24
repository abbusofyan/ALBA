<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rules;
use App\Rules\ValidPostalCode;
use App\Rules\ValidSingaporePhoneNumber;

class StoreSchoolFormRequest extends FormRequest
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
			'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
			'phone' => ['required', new ValidSingaporePhoneNumber()],
			'email' => ['required', 'string', 'email', 'max:255'],
			'password' => ['required', Rules\Password::defaults(), 'confirmed'],
			'postal_code' => ['required', new ValidPostalCode()],
			'address' => ['required'],
			'secondary_email' => 'array',
		    'secondary_email.*' => 'required|email',
        ];
    }

	public function messages() {
		return [
			'username.unique' => 'Unique ID has been taken. Please refresh to generate a new unique id',
			'secondary_email.*.required' => 'Secondary email is required or remove this field'
		];
	}

	public function attributes()
	{
	    return [
	        'phone' => 'contact',
			'name' => 'school name',
			'username' => 'unique id',
			'secondary_email.*' => 'secondary email'
	    ];
	}

}
