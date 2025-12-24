<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                      => ['required_if:event_type_id,3,4'],
			'secret_code' 				=> [
										    'required_if:event_type_id,3',
										    'nullable',
										    'regex:/^[A-Z0-9]+$/',
										    Rule::unique('events', 'secret_code')->ignore($this->route('event')),
			],
            'event_type_id'             => ['required', Rule::exists('event_types', 'id')],
            'district_id'               => ['required_if:event_type_id,1,2'],
            'address'                   => ['required_if:event_type_id,1,2'],
            // 'lat'                       => ['required'],
            // 'long'                      => ['required'],
            'date'                      => ['required'],
            'time_start'                => ['required', 'date_format:H:i'],
            'time_end'                  => ['required', 'date_format:H:i'],
            'image'                     => ['required_if:event_type_id,3,4'],
            'user_id'                   => ['required_if:event_type_id,3'],
            'description'               => ['nullable', 'string'],
            'status'                    => ['required', 'boolean'],

			'bins' => [
	            Rule::requiredIf($this->binsAreRequired()),
	            'array',
	        ],
            'bins.*.point'              => ['required_if:event_type_id,3,4', 'min:0.1'],

            'waste_types'               => ['required_if:event_type_id,1,2', 'array'],
			'waste_types.*.price' 		=> []
        ];
    }

	protected function binsAreRequired()
	{
	    return in_array($this->event_type_id, [3, 4]) && !$this->select_all_bins;
	}

	public function withValidator(Validator $validator)
	{
	    $validator->sometimes('waste_types.*.price', ['required', 'numeric', 'min:0.0001'], function ($input) {
	        return (int) $input->event_type_id === 2;
	    });
	}

    public function messages(): array
    {
        return [
            'bins.required_if' => 'Bins are required for event type 3.',
            'waste_types.required_if' => 'Waste types are required for event types 1 and 2.',
            'image.required_if' => 'Image is required for event type 3.',
            'district_id.required_if' => 'Please select district',
            'district_id.exists' => 'Please select a district',
            'lat.required' => 'Please pin a location on the map',
            'time_start.required' => 'Please select a time',
            'time_end.required' => 'Please select a time',
            'waste_types.required_if' => 'Add at least one accepted recyclables',
            'waste_types.*.price.min' => 'Please set a price for this accepted recyclable',
            'name.required_if' => 'Please enter event name',
            'secret_code.required_if' => 'Please enter event code',
            'secret_code.unique' => 'The event code has already been taken.',
            'user_id.required_if' => 'Please select an entity',
            'image.required_if' => 'Event image is required',
			'address.required_if' => 'Address field is required',
            'bins.required_if' => 'Add at least one bin',
            'bins.*.point' => 'Please set a point for this bin type',
			'secret_code.regex' => 'The event code must be uppercase letters and numbers only.',
        ];
    }
}
