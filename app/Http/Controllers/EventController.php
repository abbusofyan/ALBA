<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use App\Models\EventType;
use App\Models\District;
use App\Models\User;
use App\Models\WasteType;
use App\Models\EventWasteType;
use App\Exports\EventExport;
use App\Exports\EventTemplate\EDriveEventTemplateExport;
use App\Exports\EventTemplate\CFTEventTemplateExport;
use App\Exports\EventTemplate\PrivateEventTemplateExport;
use App\Exports\EventTemplate\ALBAEventTemplateExport;
use App\Exports\EventParticipantExport;
use App\Exports\EventCheckinBinsExport;
use App\Exports\EventRecyclingExport;
use App\Imports\EventImport;
use App\Services\EventService;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Inertia\Inertia;

use App\Http\Requests\StoreEventRequest;

class EventController extends Controller
{

	public function __construct()
	{
		$this->middleware('permission:view-event')->only(['index', 'show']);
		$this->middleware('permission:create-event')->only(['create', 'store']);
		$this->middleware('permission:update-event')->only(['edit', 'update', 'toggleStatus']);
		$this->middleware('permission:delete-event')->only(['destroy']);
	}

	public function index(Request $request)
	{
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'id';
		$sortOrder = $request->sort_order ?? 'desc';
		$eventType = $request->event_type ?? 'all';
		$status = $request->status ?? 'all';

		$filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order', 'event_type', 'status']);

		$eventsData = Event::with('type')->when($search, function ($q) use ($search) {
			$q->where('address', 'like', "%{$search}%");
			$q->orWhere('code', 'like', "%{$search}%");
			$q->orWhere('secret_code', 'like', "%{$search}%");
			$q->orWhere('description', 'like', "%{$search}%");
			$q->orWhere('date_start', 'like', "%{$search}%");
			$q->orWhere('date_end', 'like', "%{$search}%");
			$q->orWhere('name', 'like', "%{$search}%");
		})
			->when($status != 'all', function ($q) use ($status) {
				$q->where('status', $status);
			})
			->when($eventType != 'all', function ($q) use ($eventType) {
				$q->where('event_type_id', $eventType);
			})
			->orderBy($sortBy, $sortOrder)
			->paginate($paginate)
			->appends($filters);

		return Inertia::render('Event/Index', [
			'eventsData' => $eventsData,
			'filters' => $filters,
			'qty_event' => Event::count(),
			'event_types' => EventType::all()
		]);
	}

	public function create()
	{
		$eventTypes = EventType::all();
		$wasteTypes = WasteType::all();
		$districts = District::all();
		$users = User::whereHas('roles', function ($query) {
			$query->whereIn('name', ['School', 'Enterprise']);
		})->get();
		return Inertia::render('Event/Form', [
			'event_types' => $eventTypes,
			'waste_types' => $wasteTypes,
			'districts' => $districts,
			'users' => $users
		]);
	}

	public function edit(Event $event)
	{
		$event->load('type', 'eventBins.bin.type', 'eventBins.binType', 'eventWasteType.wasteType', 'district', 'user');
		$eventTypes = EventType::all();
		$wasteTypes = WasteType::all();
		$districts = District::all();
		$users = User::whereHas('roles', function ($query) {
			$query->whereIn('name', ['School', 'Enterprise']);
		})->get();
		return Inertia::render('Event/Form', [
			'event' => $event,
			'event_types' => $eventTypes,
			'waste_types' => $wasteTypes,
			'districts' => $districts,
			'users' => $users
		]);
	}

	public function store(StoreEventRequest $request)
	{
		DB::beginTransaction();
		try {
			$dates = explode(' to ', $request->date);
			$dateStart = $dates[0];
			$dateEnd = isset($dates[1]) ? $dates[1] : $dates[0];

			$image = null;
			if ($request->hasFile('image')) {
				$image = $request->file('image');
				$filename = time() . '_' . $image->getClientOriginalName();
				$path = $image->storeAs('public/images/event', $filename);

				$image = $filename;
			}

			$event = Event::create([
				'code' => Event::generateCode($request->event_type_id),
				'secret_code' => $request->secret_code,
				'event_type_id' => $request->event_type_id,
				'district_id' => $request->district_id,
				'user_id' => $request->user_id,
				'name' => $request->name,
				'address' => $request->address,
				'postal_code' => $request->postal_code,
				'lat' => $request->lat ?? '1.3521', //default center point of singapore lat
				'long' => $request->long ?? '103.8198', // default center point of singapore lng
				'date_start' => $dateStart,
				'date_end' => $dateEnd,
				'time_start' => $request->time_start,
				'time_end' => $request->time_end,
				'description' => $request->description,
				'image' => $image,
				'status' => $request->status,
				'use_all_bins' => $request->select_all_bins
			]);

			$bins = [];
			foreach ($request->bins as $bin) {
				$bins[$bin['id']] = ['point' => $bin['point']];
			}

			if ($request->event_type_id != 3 && $request->event_type_id != 4) {
				EventService::syncEventWasteTypes($event->id, $request->waste_types);
			} elseif ($request->select_all_bins) {
				$event->binTypes()->attach($bins);
			} else {
				$event->bins()->attach($bins);
			}
			DB::commit();
			return redirect('/events')->with(['success' => 'Event created successfully']);
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with(['error' => $e->getMessage()]);
		}
	}

	public function update(Event $event, StoreEventRequest $request)
	{
		DB::beginTransaction();
		try {
			$dates = explode(' to ', $request->date);
			$dateStart = $dates[0];
			$dateEnd = isset($dates[1]) ? $dates[1] : $dates[0];

			$image = $request->image;
			if ($request->hasFile('image')) {
				$image = $request->file('image');
				$filename = time() . '_' . $image->getClientOriginalName();
				$path = $image->storeAs('public/images/event', $filename);

				$image = $filename;
			}

			$event->update([
				'event_type_id' => $request->event_type_id,
				'district_id' => $request->district_id,
				'user_id' => $request->user_id,
				'name' => $request->name,
				'address' => $request->address,
				'postal_code' => $request->postal_code,
				'lat' => $request->lat,
				'long' => $request->long,
				'date_start' => $dateStart,
				'date_end' => $dateEnd,
				'time_start' => $request->time_start,
				'time_end' => $request->time_end,
				'description' => $request->description,
				'image' => $image,
				'status' => $request->status,
				'use_all_bins' => $request->select_all_bins
			]);

			$bins = [];
			foreach ($request->bins as $bin) {
				$bins[$bin['id']] = ['point' => $bin['point']];
			}

			if ($request->event_type_id != 3 && $request->event_type_id != 4) {
				EventService::syncEventWasteTypes($event->id, $request->waste_types);
			} elseif ($request->select_all_bins) {
				$event->bins()->detach();
				$event->binTypes()->sync($bins);
			} else {
				$event->binTypes()->detach();
				$event->bins()->sync($bins);
			}
			DB::commit();
			return redirect('/events')->with(['success' => 'Event updated successfully']);
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with(['error' => $e->getMessage()]);
		}
	}

	public function show(Event $event)
	{
		$event->load('eventBins.bin.type', 'eventBins.binType', 'eventWasteType.wasteType', 'type', 'district', 'user');
		$event->total_recycling = $event->recyclings()->count();
		$event->total_reward = (int)$event->recyclings()->sum('reward');
		return Inertia::render('Event/View', [
			'event' => $event,
		]);
	}

	public function recyclings(Request $request, Event $event)
	{
	    $search = $request->search;
	    $paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
	    $sortBy = $request->sort_by ?? 'recyclings.id';
	    $sortOrder = $request->sort_order ?? 'desc';

	    $recyclings = $event->recyclings()
	        ->select('recyclings.*') // important for avoiding ambiguous columns
	        ->with(['user', 'bin', 'binType'])
	        ->leftJoin('users', 'users.id', '=', 'recyclings.user_id')
	        ->leftJoin('bins', 'bins.id', '=', 'recyclings.bin_id')
	        ->when($search, function ($q) use ($search) {
	            $q->where(function ($query) use ($search) {
	                $query->whereHas('user', function ($uq) use ($search) {
	                    $uq->where('name', 'like', "%{$search}%")
	                       ->orWhere('email', 'like', "%{$search}%");
	                })
	                ->orWhereHas('bin', function ($bq) use ($search) {
	                    $bq->where('code', 'like', "%{$search}%")
	                       ->orWhere('address', 'like', "%{$search}%");
	                });
	            });
	        })
	        ->when($sortBy, function ($q) use ($sortBy, $sortOrder) {
	            switch ($sortBy) {
	                case 'user':
	                    $q->orderBy('users.first_name', $sortOrder);
	                    break;
	                case 'created_at':
	                    $q->orderBy('recyclings.created_at', $sortOrder);
	                    break;
	                case 'bins.id':
	                    $q->orderBy('bins.id', $sortOrder);
	                    break;
	                case 'reward':
	                    $q->orderBy('recyclings.reward', $sortOrder);
	                    break;
	                default:
	                    $q->orderBy('recyclings.id', $sortOrder);
	            }
	        })
	        ->paginate($paginate);

	    return response()->json($recyclings);
	}

	public function participants(Request $request, Event $event)
	{
	    $search = $request->search;
	    $paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
	    $sortBy = $request->sort_by ?? 'users.id';
	    $sortOrder = $request->sort_order ?? 'desc';

	    $recyclings = $event->participants()
	        ->when($search, function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
				   ->orWhere('first_name', 'like', "%{$search}%")
				   ->orWhere('last_name', 'like', "%{$search}%")
				   ->orWhere('phone', 'like', "%{$search}%")
				   ->orWhere('email', 'like', "%{$search}%")
				   ->orWhere('username', 'like', "%{$search}%")
				   ->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
	        })
	        ->when($sortBy, function ($q) use ($sortBy, $sortOrder) {
	            switch ($sortBy) {
	                case 'first_name':
	                    $q->orderBy('users.first_name', $sortOrder);
	                    break;
	                case 'display_name':
	                    $q->orderBy('users.display_name', $sortOrder);
	                    break;
	                case 'email':
	                    $q->orderBy('users.email', $sortOrder);
	                    break;
	                default:
	                    $q->orderBy('users.id', $sortOrder);
	            }
	        })
	        ->paginate($paginate);

	    return response()->json($recyclings);
	}

	public function checkinBins(Request $request, Event $event)
	{
	    $search = $request->search;
	    $paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
	    $sortBy = $request->sort_by ?? 'u.id';
	    $sortOrder = $request->sort_order ?? 'asc';

	    $query = \DB::table('event_recycling_logs as erl')
	        ->join('recyclings as r', 'erl.recycling_id', '=', 'r.id')
	        ->join('users as u', 'r.user_id', '=', 'u.id')
	        ->join('bin_types as bt', 'r.bin_type_id', '=', 'bt.id')
	        ->select(
	            'u.id as user_id',
				'u.phone',
				'u.display_name',
	            \DB::raw("CASE
	                WHEN u.name IS NULL OR u.name = ''
	                THEN CONCAT(u.first_name, ' ', u.last_name)
	                ELSE u.name
	            END as user_name"),
	            'u.email',
	            // ✅ Count real bins + virtual bins (NULL bin_id grouped by bin_type)
	            \DB::raw("COUNT(DISTINCT COALESCE(r.bin_id, CONCAT('empty_', r.bin_type_id))) as total_unique_bins"),
	            // ✅ Group bin type + bin_id (with fallback)
	            \DB::raw("GROUP_CONCAT(DISTINCT CONCAT(bt.name, ' : ', COALESCE(r.bin_id, CONCAT('empty_', r.bin_type_id))) SEPARATOR ';') as bin_breakdown")
	        )
	        ->where('erl.event_id', $event->id)
	        ->groupBy('u.id', 'u.name', 'u.first_name', 'u.last_name', 'u.email');

	    if ($search) {
	        $query->where(function ($q) use ($search) {
	            $q->where('u.name', 'like', "%{$search}%")
	              ->orWhere('u.first_name', 'like', "%{$search}%")
				  ->orWhere('u.last_name', 'like', "%{$search}%")
				  ->orWhere('u.display_name', 'like', "%{$search}%")
				  ->orWhere('u.phone', 'like', "%{$search}%")
				  ->orWhere(\DB::raw("CONCAT(u.first_name, ' ', u.last_name)"), 'like', "%{$search}%")
				  ->orWhere('u.email', 'like', "%{$search}%");
	        });
	    }

	    // Sorting
	    $query->orderBy($sortBy, $sortOrder);

	    $users = $query->paginate($paginate);

	    // ✅ Format breakdown into: [ "Battery" => 2, "Commingled Recycling Bin" => 1, ... ]
	    $users->getCollection()->transform(function ($item) {
	        $breakdown = [];
	        if ($item->bin_breakdown) {
	            $pairs = explode(';', $item->bin_breakdown);
	            foreach ($pairs as $pair) {
	                [$type, $binKey] = explode(' : ', $pair);
	                $breakdown[$type] = ($breakdown[$type] ?? 0) + 1;
	            }
	        }
	        $item->bin_type_breakdown = $breakdown;
	        unset($item->bin_breakdown);
	        return $item;
	    });

	    return response()->json($users);
	}



	public function destroy(Event $event)
	{
		$event->delete();
		return response()->json([
			'success' => true,
			'message' => 'Event has been deleted successfully'
		]);
	}

	public function export()
	{
		return Excel::download(new EventExport, 'Export event data ' . date('Y-m-d') . '.xlsx', ExcelFormat::XLSX);
	}

	public function downloadParticipant(Event $event)
	{
		$event->load('type', 'participants');
		return Excel::download(new EventParticipantExport($event), $event->type->name . ' ' . $event->code . ' Participants.xlsx', ExcelFormat::XLSX);
	}

	public function downloadCheckinBins(Event $event)
	{
		$event->load('type');
		return Excel::download(new EventCheckinBinsExport($event), $event->type->name . ' ' . $event->code . ' Check-in Bins.xlsx', ExcelFormat::XLSX);
	}

	public function downloadRecyclings(Event $event)
	{
		$event->load([
		    'recyclings' => function ($query) {
		        $query->select('recyclings.id', 'recyclings.bin_id', 'recyclings.bin_type_id', 'recyclings.user_id', 'recyclings.reward', 'recyclings.created_at');
		    },
		    'recyclings.user' => function ($query) {
		        $query->select('users.id', 'users.first_name', 'users.last_name', 'users.display_name', 'users.phone', 'users.email');
		    },
			'recyclings.bin' => function ($query) {
		        $query->select('bins.code');
		    },
			'recyclings.binType' => function ($query) {
		        $query->select('bin_types.id', 'bin_types.name');
		    },
		]);
		return Excel::download(new EventRecyclingExport($event), $event->type->name . ' ' . $event->code . ' Recycling Activities.xlsx', ExcelFormat::XLSX);
	}

	public function toggleStatus(Request $request, Event $event)
	{
		if ($event->status) {
			$event->update(['status' => 0]);
			$message = 'Event has been deactivated';
		} else {
			$event->update(['status' => 1]);
			$message = 'Event has been activated';
		}
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}

	public function downloadTemplateView() {
		return Inertia::render('Event/DownloadTemplate');
	}

	public function downloadTemplate($eventTypeId)
	{
		if ($eventTypeId == 1) {
			return (new EDriveEventTemplateExport)->download('E-Drive import Template.xlsx', ExcelFormat::XLSX);
		}

		if ($eventTypeId == 2) {
			return (new CFTEventTemplateExport)->download('Cash For Traash import Template.xlsx', ExcelFormat::XLSX);
		}

		if ($eventTypeId == 3) {
			return (new PrivateEventTemplateExport)->download('Private Event import Template.xlsx', ExcelFormat::XLSX);
		}

		if ($eventTypeId == 4) {
			return (new ALBAEventTemplateExport)->download('ALBA Event import Template.xlsx', ExcelFormat::XLSX);
		}
	}

	public function import(Request $request)
	{
		$request->validate([
			'file' => 'required|mimes:xlsx,xls,csv|max:2048',
		]);

		try {
			DB::transaction(function () use ($request) {
	            Excel::import(new EventImport($request->event_type_id), $request->file('file'));
	        });
			return response()->json(['message' => 'File imported successfully.']);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}
}
