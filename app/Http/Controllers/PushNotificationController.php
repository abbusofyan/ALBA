<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PushNotification;
use App\Http\Requests\PushNotificationRequest;
use App\Jobs\SendPushNotification;
use App\Services\FirebaseService;
use DateTime;

class PushNotificationController extends Controller
{
	public function index(Request $request)
    {
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'id';
		$sortOrder = $request->sort_order ?? 'desc';
		$status = $request->status ?? 'all';

		$filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order', 'status']);

		$notificationsData = PushNotification::when($search, function ($q) use ($search) {
			$q->where('title', 'like', "%{$search}%")
				  ->orWhere('body', 'like', "%{$search}%")
				  ->orWhere('scheduled_at', 'like', "%{$search}%");
		    })
		    ->orderBy($sortBy, $sortOrder)
		    ->paginate($paginate)
		    ->appends($filters);

		return Inertia::render('PushNotification/Index', [
		    'notificationsData' => $notificationsData,
		    'filters' => $filters,
		    'qty_notifications' => PushNotification::count(),
		]);
    }

	public function create() {
		return Inertia::render('PushNotification/Form');
	}

	public function edit(PushNotification $push_notification) {
		return Inertia::render('PushNotification/Form', ['notification' => $push_notification]);
	}

	public function store(PushNotificationRequest $request) {
		$data = $request->all();
		$data['status'] = PushNotification::STATUS_PENDING;
		if (!$data['send_now']) {
			$data['scheduled_at'] = DateTime::createFromFormat('Y-m-d H:i', $data['scheduled_at'])->format('Y-m-d H:i:s');
		}
		$notification = PushNotification::create($data);

		if ($notification->scheduled_at) {
		    SendPushNotification::dispatch($notification)
		        ->delay($notification->scheduled_at);
		} else {
		    SendPushNotification::dispatch($notification);
		}

		return redirect('/push-notifications')->with(['success' => 'Notification saved']);
	}

	public function update(Request $request, PushNotification $push_notification) {
		$data = $request->all();
		dd($data);
		if ($data['send_now']) {
			$data['scheduled_at'] = null;
		} else {
			$data['scheduled_at'] = DateTime::createFromFormat('Y-m-d H:i', $data['scheduled_at'])->format('Y-m-d H:i:s');
		}
		$push_notification->update($data);

		if ($notification->scheduled_at) {
		    SendPushNotification::dispatch($notification)
		        ->delay($notification->scheduled_at);
		} else {
		    SendPushNotification::dispatch($notification);
		}
		return redirect('/push-notifications')->with(['success' => 'Notification updated']);
	}
}
