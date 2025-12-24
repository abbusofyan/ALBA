<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Smalot\PdfParser\Parser;

class TermsAndConditionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/tnc",
     *     tags={"T&C"},
     *     summary="Get T&C",
     *     description="Get T&C",
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function index()
    {
        $parser = new Parser();

        $pdf = $parser->parseFile(public_path('tnc.pdf')); // or $request->file('pdf')->getPathname()

        $text = $pdf->getText();
        return ApiResponse::success([
            'text' => $text,
        ], 'Terms and Conditions retrieved successfully');
    }
}
