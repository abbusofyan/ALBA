<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use App\Models\ServiceOption;
use App\Helpers\ApiResponse;
use OpenApi\Annotations as OA;

class ServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/service/get-options",
     *     tags={"Services"},
     *     summary="Get all service options",
     *     description="Fetches a list of available service options.",
     *     @OA\Response(
     *         response=200,
     *         description="List of service options",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="service options fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Premium Support"),
     *                     @OA\Property(property="description", type="string", example="24/7 priority service"),
     *                     @OA\Property(property="price", type="number", format="float", example=99.99)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getOptions() {
        $serviceOptions = ServiceOption::all();
        return ApiResponse::success($serviceOptions, 'service options fetched successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/service/submit",
     *     tags={"Services"},
     *     summary="Submit a new service request",
     *     description="Creates a new service request based on the provided option and message.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"service_option_id", "message"},
     *             @OA\Property(property="service_option_id", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Need urgent setup assistance")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service submitted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Service submitted successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="service",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="service_option_id", type="integer", example=1),
     *                     @OA\Property(property="message", type="string", example="Need urgent setup assistance"),
     *                     @OA\Property(
     *                         property="option",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Premium Support")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function submit(StoreServiceRequest $request) {
        $service = Service::create([
            'service_option_id' => $request->service_option_id,
            'message' => $request->message
        ]);
        $service->load('option');
        return ApiResponse::success(['service' => $service], 'Service submitted successfully');
    }
}
