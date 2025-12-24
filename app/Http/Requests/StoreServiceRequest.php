<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPostalCode;
use App\Rules\ValidSingaporePhoneNumber;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'service_option_id' => 'required',
            'message'            => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'service_option_id.required' => 'Please select nature of query.',
            'message.required'            => 'Message cannot be empty.',
        ];
    }

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(
	        ApiResponse::validation($validator->errors())
	    );
	}
}
