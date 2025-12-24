<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'banners' => 'array',
            'banners.*.placement_id' => 'required|integer',
            'banners.*.url' => 'nullable|url',
            // 'banners.*.image' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'banners.*.placement_id.required' => 'Select banner placement',
            'banners.*.url.url' => 'URL invalid',
        ];
    }

    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         $banners = $this->input('banners', []);
	//
    //         foreach ($banners as $index => $banner) {
    //             $hasPreview = $banner['preview'] ?? null;
    //             $hasImage = $banner['image'] ?? null;
	//
    //             if (empty($hasPreview)) {
    //                 $validator->errors()->add("banners.$index.image", 'Image is required.');
    //             }
    //         }
    //     });
    // }
}
