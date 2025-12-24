<?php

namespace App\Services;

use App\Models\Bin;
use App\Models\WasteType;
use App\Models\RVMTransaction;
use App\Models\Recycling;
use App\Models\RVMTransactionQueue;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BinService
{
	public static function get($filters)
	{
		$wasteTypes = WasteType::All()->pluck('name')->toArray();

	    $binTypeIds = [];
	    $wasteTypeIds = [];
	    $search = $filters['accepted_recyclables'] ?? null;
	    $userLat = $filters['lat'] ?? null;
	    $userLng = $filters['long'] ?? null;
	    $visibility = $filters['visibility'] ?? null;
		foreach ($wasteTypes as $wasteType) {
		    $distance = levenshtein(strtolower($search), strtolower($wasteType));
		    if ($distance <= 2) { // allow up to 2 edits
				$search = $wasteType;
				continue;
		    }
		}

	    if (!empty($filters['bin_type_id'])) {
	        $binTypeIds = is_array($filters['bin_type_id']) ? $filters['bin_type_id'] : explode(',', $filters['bin_type_id']);
	    }

	    if (!empty($filters['waste_type_id'])) {
	        $wasteTypeIds = is_array($filters['waste_type_id']) ? $filters['waste_type_id'] : explode(',', $filters['waste_type_id']);
	    }

	    $binQuery = Bin::select([
				'id',
				'code',
	            'bin_type_id',
	            'address',
	            'lat',
	            'long',
	            'map_radius',
	            'status',
				'visibility'
	        ])
			->when($visibility == Bin::SHOWN, function ($query) {
	            $query->where('visibility', Bin::SHOWN);
	        })
	        ->when(!empty($binTypeIds), function ($query) use ($binTypeIds) {
	            $query->whereIn('bin_type_id', $binTypeIds);
	        })
	        ->when(!empty($wasteTypeIds), function ($query) use ($wasteTypeIds) {
	            $query->whereHas('type.wasteTypes', function ($q) use ($wasteTypeIds) {
	                $q->whereIn('waste_types.id', $wasteTypeIds);
	            });
	        })
	        ->when(!empty($search), function ($query) use ($search) {
	            $query->whereHas('type.wasteTypes', function ($q) use ($search) {
	                $q->where('waste_types.name', 'LIKE', '%' . $search . '%');
	            });
	        })
	        ->with(['type' => function ($query) {
	            $query->select(['id', 'name', 'image', 'icon'])->with([
	                'wasteTypes' => function ($wasteQuery) {
	                    $wasteQuery->select(['waste_types.id', 'waste_types.name']);
	                }
	            ]);
	        }]);

	    if (!empty($userLat) && !empty($userLng)) {
	        $binQuery->addSelect(\DB::raw("(
	            6371 * acos(
	                cos(radians($userLat)) *
	                cos(radians(lat)) *
	                cos(radians(`long`) - radians($userLng)) +
	                sin(radians($userLat)) *
	                sin(radians(lat))
	            )
	        ) AS distance"))
	        ->orderBy('distance', 'asc');
	    }

	    $binLocations = $binQuery->get();

	    return $binLocations;
	}



	public static function getNerbyBinLocations($lat, $lng) {
		$radius = 1; // in km

		$binLocations = Bin::select([
		        'id',
		        'bin_type_id',
		        'address',
		        'lat',
		        'long',
		        'map_radius',
		        'status',
		        'qrcode',
		        DB::raw("(6371 * acos(
		            cos(radians($lat)) *
		            cos(radians(lat)) *
		            cos(radians(`long`) - radians($lng)) +
		            sin(radians($lat)) *
		            sin(radians(lat))
		        )) AS distance"),
		    ])
		    ->having("distance", "<", $radius)
		    ->orderBy("distance")
		    ->with([
		        'type:id,name,image,icon',
		        'type.wasteTypes:id,name',
		    ])
		    ->get();
		return $binLocations;
	}

	public static function getMapLocation() {
		$binLocations = Bin::select(
		    'postal_code',
		    DB::raw('AVG(lat) as lat'),
		    DB::raw('AVG(`long`) as `long`'),
		    DB::raw('COUNT(*) as total')
		)->groupBy('postal_code')->get();
		return $binLocations;
	}

	public static function submitRVMTransaction(User $user, Bin $bin, string $qrCodeValue, array $data, string $rvmType)
	{
	    return DB::transaction(function () use ($user, $bin, $qrCodeValue, $data, $rvmType) {

	        // 🔒 Try to fetch existing transaction row and lock it
	        $queue = RVMTransactionQueue::where('qrcode', $qrCodeValue)->lockForUpdate()->first();

			if ($queue && $queue->status === RVMTransactionQueue::STATUS_SUCCESS) {
	            return false;
	        }

	        if ($queue) {
	            // Update existing record
	            $queue->update([
	                'status'      => RVMTransactionQueue::STATUS_SUCCESS,
	                'last_result' => json_encode($data),
	            ]);
	        } else {
	            // Create new record
	            RVMTransactionQueue::create([
	                'user_id'     => $user->id,
	                'bin_id'      => $bin->id,
	                'qrcode'      => $qrCodeValue,
	                'type'        => $rvmType,
	                'status'      => RVMTransactionQueue::STATUS_SUCCESS,
	                'last_result' => json_encode($data),
	            ]);
	        }

	        // Get reward
	        $reward = $data['deposit'] ?? 0;

			$exists = Recycling::where('user_id', $user->id)
	            ->where('bin_id', $bin->id)
	            ->whereBetween('created_at', [now()->subSeconds(2), now()]) //to prevent race condition
	            ->exists();

	        if (!$exists) {
	            Recycling::create([
	                'user_id'     => $user->id,
	                'bin_id'      => $bin->id,
	                'bin_type_id' => $bin->bin_type_id,
	                'reward'      => $reward,
	            ]);

	            $user->increment('point', $reward);
	        }

	        return true;
	    });
	}


}
