<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPostalCode;
use App\Rules\ValidSingaporePhoneNumber;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nature_of_query' => 'required|string',
            'name'            => 'required|string',
            'email'           => 'required|email',
			'phone' 		  => ['required', 'numeric', 'digits:8'],
            'address'         => 'required|string',
			'postal_code' 	  => ['required', new ValidPostalCode()],
            'message'         => 'required',
			'attachments'     => 'nullable|array',
		    'attachments.*'   => 'image|mimes:jpeg,png,jpg,gif|max:20480', // each max 2MB
        ];
    }

    public function messages()
    {
        return [
            'nature_of_query.required' => 'Please select the nature of your query.',
            'name.required'            => 'Please enter your name.',
            'email.required'           => 'Please enter your email address.',
            'email.email'              => 'Please enter a valid email address.',
            'phone.required'           => 'Please enter your phone number.',
            'phone.digits'             => 'Phone number must be exactly 8 digits.',
            'address.required'         => 'Please enter your address.',
            'postal_code.required'     => 'Please enter your postal code.',
            'message.required'         => 'Please enter your message.',
        ];
    }

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(
	        ApiResponse::validation($validator->errors())
	    );
	}
}
