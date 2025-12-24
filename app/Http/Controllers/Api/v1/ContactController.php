<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Http\Requests\ContactFormRequest;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/contact/send",
     *     tags={"Contact"},
     *     summary="Submit contact form",
     *     description="Submit a contact form with query details and optional image attachments",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={
     *                     "nature_of_query",
     *                     "name",
     *                     "email",
     *                     "phone",
     *                     "address",
     *                     "postal_code",
     *                     "message"
     *                 },
     *                 @OA\Property(property="nature_of_query", type="string", example="Product Inquiry"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *                 @OA\Property(property="phone", type="string", example="12345678", description="8-digit phone number"),
     *                 @OA\Property(property="address", type="string", example="123 Orchard Road, Singapore"),
     *                 @OA\Property(property="postal_code", type="string", example="123456"),
     *                 @OA\Property(property="message", type="string", example="I would like more information about your services."),
     *                 @OA\Property(
     *                     property="attachments[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Multiple image attachments (JPEG, PNG, JPG, GIF, max 2MB each)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Contact form sent successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"email": {"The email field must be a valid email address."}}
     *             )
     *         )
     *     )
     * )
     */
    public function send(ContactFormRequest $request)
    {
        try {
            $formData = $request->except('attachments');

            Mail::send('emails.contact', ['data' => $formData], function ($message) use ($request) {
                $message->to(config('mail.contact', 'contact.sg@albagroup.asia'))
                        ->subject('New Contact Form Submission');

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        if ($file->isValid()) {
                            $message->attach(
                                $file->getRealPath(),
                                [
                                    'as' => $file->getClientOriginalName(),
                                    'mime' => $file->getMimeType(),
                                ]
                            );
                        }
                    }
                }
            });

            return ApiResponse::success(null, 'Contact form sent successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}
