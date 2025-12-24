<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Smalot\PdfParser\Parser;

class PrivacyPolicyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/privacy-policy",
     *     tags={"Privacy Policy"},
     *     summary="Get Privacy Policy",
     *     description="Get Privacy Policy",
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function index()
    {
        $parser = new Parser();

        $pdf = $parser->parseFile(public_path('privacy_policy.pdf')); // or $request->file('pdf')->getPathname()

        $text = $pdf->getText();
        return ApiResponse::success([
            'text' => $text,
        ], 'Privacy Policy retrieved successfully');
    }
}
