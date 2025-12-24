<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;
use App\Models\PickUpWasteType;

class SubmitPickUpOrderRequest extends FormRequest
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
			'waste_type_id'      => 'required|exists:pick_up_waste_types,id',
			'weight_range_id' => [
	            'nullable',
	            Rule::requiredIf(fn () =>
	                in_array($this->waste_type_id, [PickUpWasteType::GENERAL_PAPER, PickUpWasteType::CONFIDENTIAL_PAPER])
	            ),
	            Rule::when(
	                fn () => in_array($this->waste_type_id, [PickUpWasteType::GENERAL_PAPER, PickUpWasteType::CONFIDENTIAL_PAPER]),
	                fn () => Rule::exists('pick_up_weight_ranges', 'id')
	            ),
	        ],
			'quantity'           => 'required_if:waste_type_id,3',
			'e_waste_description'=> 'required_if:waste_type_id,3',
			'photo'              => 'nullable',
			'remark'             => 'nullable',
			'time_slot_id'       => 'required|exists:pick_up_time_slots,id',
			'pickup_date'        => 'required|date_format:Y-m-d',
			'address'            => 'required',
		];
	}


    /**
     * Custom messages (optional)
     */
    public function messages()
    {
        return [
			'pickup_date.required' => 'Please select pickup date',
			'waste_type_id.required' => 'Please select waste category',
            'weight_range_id.required_if' => 'Please select weight range.',
            'quantity.required_if' => 'Please input quantity.',
            'e_waste_description.required_if' => 'Please input types of E-Waste',
        ];
    }

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(
	        ApiResponse::validation($validator->errors())
	    );
	}
}
