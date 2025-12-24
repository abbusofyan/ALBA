<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\District;
use App\Models\Bin;
use App\Models\BinType;
use App\Models\Event;

class AutoCompleteController extends Controller
{
	public function user(Request $request)
	{
		$query = $request->get('q');

		$entities = User::query()
			->when($query, function ($qBuilder) use ($query) {
				$qBuilder->where('name', 'like', '%' . $query . '%');
				$qBuilder->orWhere('username', 'like', '%' . $query . '%');
			})
			->orderBy('name')
			->limit(100)
			->get(['id', 'name']);

		return response()->json($entities);
	}

	public function district(Request $request)
	{
		$query = $request->get('q');

		$districts = District::query()
			->when($query, function ($qBuilder) use ($query) {
				$qBuilder->where('name', 'like', '%' . $query . '%');
				$qBuilder->orWhere('region', 'like', '%' . $query . '%');
			})
			->orderBy('name')
			->limit(100)
			->get(['id', 'name']);

		return response()->json($districts);
	}

	public function bin(Request $request)
	{
	    $query = $request->get('q', '');
	    $perPage = $request->get('per_page', 25);

	    $bins = Bin::with(['type:id,name,point'])
	        ->where('bin_type_id', '!=', 1)
	        ->when($query, function ($qBuilder) use ($query) {
	            $qBuilder->where(function ($subQuery) use ($query) {
	                $subQuery->where('address', 'like', '%' . $query . '%')
	                    ->orWhere('code', 'like', '%' . $query . '%')
	                    ->orWhereHas('type', function ($typeQuery) use ($query) {
	                        $typeQuery->where('name', 'like', '%' . $query . '%');
	                    });
	            });
	        })
	        ->orderBy('created_at', 'desc')
	        ->paginate($perPage);

	    return response()->json($bins);
	}

	public function binType(Request $request)
	{
		$query = $request->get('q');

		$bins = BinType::where('id', '!=', 1)->when($query, function ($qBuilder) use ($query) {
		        $qBuilder->where(function ($subQuery) use ($query) {
		            $subQuery->where('name', 'like', '%' . $query . '%');
		        });
		    })
		    ->get();

		return response()->json($bins);
	}

	public function event(Request $request)
	{
		$query = $request->get('q');

		$events = Event::with(['type:id,name', 'district:id,name'])
			->when($query, function ($qBuilder) use ($query) {
				$qBuilder->where(function ($subQuery) use ($query) {
					$subQuery->where('name', 'like', '%' . $query . '%')
						->orWhere('description', 'like', '%' . $query . '%')
						->orWhere('code', 'like', '%' . $query . '%')
						->orWhereHas('type', function ($typeQuery) use ($query) {
							$typeQuery->where('name', 'like', '%' . $query . '%');
						})
						->orWhereHas('district', function ($districtQuery) use ($query) {
							$districtQuery->where('name', 'like', '%' . $query . '%');
						});
				});
			})
			->limit(100)
			->get();

		return response()->json($events);
	}

	public function privateAndAlbaEvent(Request $request)
	{
	    $query = $request->get('q');

	    $events = Event::with(['type:id,name', 'district:id,name'])
	        ->whereIn('event_type_id', [3, 4]) // <-- Added this condition
	        ->when($query, function ($qBuilder) use ($query) {
	            $qBuilder->where(function ($subQuery) use ($query) {
	                $subQuery->where('name', 'like', '%' . $query . '%')
	                    ->orWhere('description', 'like', '%' . $query . '%')
	                    ->orWhere('code', 'like', '%' . $query . '%')
	                    ->orWhereHas('type', function ($typeQuery) use ($query) {
	                        $typeQuery->where('name', 'like', '%' . $query . '%');
	                    })
	                    ->orWhereHas('district', function ($districtQuery) use ($query) {
	                        $districtQuery->where('name', 'like', '%' . $query . '%');
	                    });
	            });
	        })
	        ->limit(100)
	        ->get();

	    return response()->json($events);
	}


}
