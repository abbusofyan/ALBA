<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PushNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // allow for now, or check permissions
    }

    public function rules(): array
    {
        return [
            'title'   => ['required', 'string', 'max:255'],
            'body'    => ['required', 'string'],
            'scheduled_at'    => $this->send_now
                ? ['nullable'] // if sendNow = true, no date required
                : ['required', 'after_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Notification title is required.',
            'body.required'  => 'Notification body is required.',
            'scheduled_at.required'  => 'Please select a schedule date if not sending now.',
        ];
    }
}
