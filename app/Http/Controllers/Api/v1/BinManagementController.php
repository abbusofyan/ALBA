<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bin;
use App\Models\RVMTransaction;
use App\Models\RVMTransactionQueue;
use App\Helpers\ApiResponse;
use App\Services\BinService;
use App\Services\RVMSystemService;
use App\Services\EnvipcoService;
use App\Helpers\Helper;

class BinManagementController extends Controller
{
	/**
	 * @OA\Post(
	 *     path="/api/v1/bin-management/getAll",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Bin Management"},
	 *     summary="Get bins",
	 *     description="Get bins with optional filtering by bin_type_id, waste_type_id (arrays), and search keyword in waste type name.",
	 *     @OA\Parameter(
	 *         name="bin_type_id[]",
	 *         in="query",
	 *         required=false,
	 *         description="Filter by one or more bin type IDs",
	 *         @OA\Schema(
	 *             type="array",
	 *             @OA\Items(type="integer")
	 *         ),
	 *         style="form",
	 *         explode=true
	 *     ),
	 *     @OA\Parameter(
	 *         name="waste_type_id[]",
	 *         in="query",
	 *         required=false,
	 *         description="Filter by one or more waste type IDs",
	 *         @OA\Schema(
	 *             type="array",
	 *             @OA\Items(type="integer")
	 *         ),
	 *         style="form",
	 *         explode=true
	 *     ),
	 *     @OA\Parameter(
	 *         name="accepted_recyclables",
	 *         in="query",
	 *         required=false,
	 *         description="Search keyword to match accepted recyclables",
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Parameter(
	 *         name="lat",
	 *         in="query",
	 *         required=false,
	 *         description="User's current latitude",
	 *         @OA\Schema(type="number", format="float")
	 *     ),
	 *     @OA\Parameter(
	 *         name="long",
	 *         in="query",
	 *         required=false,
	 *         description="User's current longitude",
	 *         @OA\Schema(type="number", format="float")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Bin locations retrieved successfully"
	 *     ),
	 *     @OA\Response(
	 *         response=500,
	 *         description="Internal server error"
	 *     )
	 * )
	 */


	public function getAll(Request $request)
	{
	    try {
	        $binLocations = BinService::get(array_merge($request->all(), ['visibility' => Bin::SHOWN]));
	        return ApiResponse::success(['locations' => $binLocations], 'Bin locations retrieved successfully');
	    } catch (\Exception $e) {
	        return ApiResponse::error($e->getMessage(), 500);
	    }
	}

	/**
	 * @OA\Get(
	 *     security={{"bearerAuth":{}}},
	 *     path="/api/v1/bin-management/get/{bin}",
	 *     tags={"Bin Management"},
	 *     summary="Get a specific bin by ID",
	 *     description="Get bin detail by its ID",
	 *     @OA\Parameter(
	 *         name="bin",
	 *         in="path",
	 *         required=true,
	 *         description="ID of the bin to retrieve",
	 *         @OA\Schema(type="integer")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string"),
	 *             @OA\Property(property="data", type="object")
	 *         )
	 *     )
	 * )
	 */
	public function get($binId)
	{
	    try {
			$bin = Bin::find($binId);
			if (!$bin) {
				throw new \Exception("Bin not found in the system", 404);
			}

			if ($bin->status == Bin::STATUS_INACTIVE) {
				throw new \Exception("Bin is inactive", 409);
			}

			$bin->load('type.wasteTypes');
			return ApiResponse::success($bin, 'Bin detail retrieved successfully');
	    } catch (\Exception $e) {
			return ApiResponse::error($e->getMessage());
	    }
	}

	/**
	 * @OA\Get(
	 *     security={{"bearerAuth":{}}},
	 *     path="/api/v1/bin-management/scan",
	 *     tags={"Bin Management"},
	 *     summary="Scan bin qr code",
	 *     description="scan bin qr code",
	 *     @OA\Parameter(
	 *         name="qrcode_value",
	 *         in="query",
	 *         required=true,
	 *         description="QRCode value",
	 *         @OA\Schema(type="string", format="string")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string"),
	 *             @OA\Property(property="data", type="object")
	 *         )
	 *     )
	 * )
	 */

	public function scan(Request $request)
	{
		$user = resolve('user');
		$qrCodeValue = Helper::getRawQueryParam('qrcode_value');
	    try {
			$bin = $this->validateQrcode($qrCodeValue);

			if (strpos($qrCodeValue, '/rvm/')) {
				$rvmSystemService = new RVMSystemService;
				return $rvmSystemService->scan($user, $qrCodeValue);
			};

			if (strpos($qrCodeValue, '/envipco/')) {
				$envipcoService = new EnvipcoService;
				// if ($envipcoService->isRedeemed($user, $qrCodeValue)) {
				// 	throw new \Exception('QR Code has already been scanned.', 500);
				// }
				return $envipcoService->scan($user, $qrCodeValue);
			};

			return ApiResponse::success($bin, 'Bin detail retrieved successfully');
	    } catch (\Exception $e) {
			\Log::info([
				'user_id' => $user->id,
				'scanning data' => $request->all()
			]);
			return ApiResponse::error($e->getMessage());
	    }
	}

	private function validateQrcode($qrCodeValue) {
	    if (!$qrCodeValue) {
	        throw new \Exception('QR Code Value parameter is required.', 500);
	    }

		$bin = null;

		if (strpos($qrCodeValue, '/rvm/')) {
			$decoded = RVMSystemService::decode($qrCodeValue);
			$bin = Bin::where('code', $decoded['rvm_id'])->first();
		} elseif (strpos($qrCodeValue, '/envipco/')) {
			$decoded = EnvipcoService::decode($qrCodeValue);
			$bin = Bin::where('code', $decoded['rvm_id'])->first();
		} else {
			if (strpos($qrCodeValue, 'https://step-up.sg/recycling-bin/') !== false) {
				$bin = Bin::with('type.wasteTypes')->find(4173); // client ask to use this temp bin
			} else {
				$bin = Bin::with('type.wasteTypes')->where('qrcode', $qrCodeValue)->orWhere('code', $qrCodeValue)->first();
			}
		}

		if (!$bin) {
			throw new \Exception('Bin not found in the system.', 404);
		}

		if ($bin->status == Bin::STATUS_INACTIVE) {
			throw new \Exception('Bin is inactive at the moment. Try again later.', 500);
		}

		// $rvmTransaction = RVMTransaction::where('qrcode', $qrCodeValue)->exists();
		$rvmTransactionQueue = RVMTransactionQueue::where('qrcode', $qrCodeValue)->exists();
		if ($rvmTransactionQueue) {
			throw new \Exception('QR Code has already been scanned.', 500);
		}

		return $bin;
	}

	public function getAllBinIds(Request $request)
	{
		$binTypeIds = $request->bin_type_id;
	    $query = Bin::query();

	    $query->where('bin_type_id', '!=', 1);

	    if ($binTypeIds) {
	        $query->whereIn('bin_type_id', $binTypeIds);
	    }

	    $binIds = $query->pluck('id');
		return $binIds;
	}

	public function pingEnvipco() {
		$envipcoService = new EnvipcoService;
		return $envipcoService->ping();
	}

}
