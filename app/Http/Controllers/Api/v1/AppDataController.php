<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use App\Models\AppData;
use Illuminate\Http\Request;

class AppDataController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/app-data",
     *     tags={"App Data"},
     *     summary="Get All App Data",
     *     description="Returns a list of all App Data records.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful Operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function all()
    {
        $appData = AppData::orderBy('key', 'asc')->get();

        return $this->result($appData);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/app-data/get-by-key",
     *     tags={"App Data"},
     *     summary="Get App Data by Key",
     *     description="Returns App Data record for a specific key.",
     *        @OA\RequestBody(
     *             required=true,
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                        property="key",
     *                        type="string",
     *                        description="Name of key",
     *                        example="Banner"
     *                    )
     *                 )
     *             )
     *         ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful Operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="App Data Not Found",
     *     )
     * )
     */
    public function getByKey(Request $request)
    {
        $request->validate([
            'key' => 'required|string|exists:app_data,key',
        ]);

        $key = $request->input('key');
        $appData = AppData::where('key', $key)->first();

        if (!$appData) {
            return $this->result('', 'App Data not found', 404);
        }

        return $this->result($appData);
    }
}
