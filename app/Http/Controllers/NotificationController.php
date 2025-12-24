<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

/**
 * NotificationController
 *
 * This controller manages CRUD operations for notifications,
 * including sending push notifications to all users (via a topic)
 * or to a specific user (via their device tokens).
 * It also provides a function to send custom reminder notifications
 * from other models to a specific user.
 */
class NotificationController extends Controller
{
    public function __construct()
    {
//        $this->authorizeResource(Notification::class, 'notification');
    }

    private function userIdRule(): array
    {
        return [
            'nullable',
            function ($attribute, $value, $fail) {
                if ($value !== 'all' && !User::where('id', $value)->exists()) {
                    $fail('The selected user is invalid.');
                }
            }
        ];
    }

    private function handleIcon(Request $request, &$data, $oldIcon = null)
    {
        if ($request->hasFile('icon_url')) {
            if ($oldIcon && Storage::disk('public')->exists($oldIcon)) {
                Storage::disk('public')->delete($oldIcon);
            }
            $data['icon_url'] = $request->file('icon_url')->store('icons/notifications', 'public');
        }
    }

    private function normalizeUserId(&$data)
    {
        if ($data['user_id'] === 'all') $data['user_id'] = null;
    }

    /**
     * Display a list of notifications
     */
    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->search,
            'order' => $request->order ?? 'id',
            'by' => $request->by ?? 'desc',
            'paginate' => $request->paginate ?? 10
        ];

        $notifications = Notification::with('user')
            ->when($filters['search'], fn($q, $search) => $q->where('title', 'LIKE', "%{$search}%"))
            ->orderBy($filters['order'], $filters['by'])
            ->paginate($filters['paginate'])->appends($filters);

        return Inertia::render('Notification/Index', compact('notifications', 'filters'));
    }

    /**
     * Show the form for creating a new notification
     */
    public function create(): Response
    {
        return Inertia::render('Notification/Form', [
            'csrf' => csrf_token(),
        ]);
    }

    /**
     * Store a newly created notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', Rule::unique('notifications', 'title')->whereNull('deleted_at')],
            'message' => 'required|string',
            'icon_url' => 'nullable|image',
            'is_active' => 'required|boolean',
            'user_id' => $this->userIdRule(),
        ]);

        $data = $request->except('icon_url');
        $this->handleIcon($request, $data);
        $this->normalizeUserId($data);
        Notification::create($data);

        return redirect('/notifications')->with('created', 'Notification created successfully');
    }

    /**
     * Show the form for editing the specified notification
     */
    public function edit(Notification $notification): Response
    {
        return Inertia::render('Notification/Form', [
            'csrf' => csrf_token(),
            'notification' => $notification->load('user'),
        ]);
    }

    /**
     * Update the specified notification
     */
    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title' => [
                'required',
                'string',
                Rule::unique('notifications', 'title')
                    ->ignore($notification->id)
                    ->whereNull('deleted_at'),
            ],
            'message' => 'required|string',
            'icon_url' => 'nullable|image',
            'is_active' => 'required|boolean',
            'user_id' => $this->userIdRule(),
        ]);

        $data = $request->except('icon_url');
        $this->handleIcon($request, $data, $notification->icon_url);
        $this->normalizeUserId($data);
        $notification->update($data);

        return redirect('/notifications')->with('updated', 'Notification updated successfully');
    }

    /**
     * Remove the specified notification
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        $notification->delete();
        return back()->with('deleted', 'Notification deleted successfully');
    }

    /**
     * Push the notification via Firebase Cloud Messaging
     */
    public function push(Notification $notification)
    {
        //$this->authorize('create', $notification);
        $result = $this->sendPushNotification($notification);
        return back()->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Notification pushed successfully.' : $result['error']);
    }

    /**
     * Send push notification for a notification model
     */
    private function sendPushNotification(Notification $notification)
    {
        $credentialsPath = storage_path('app/firebase/firebase-service-account.json');

        $factory = (new \Kreait\Firebase\Factory())
            ->withServiceAccount($credentialsPath);
        $messaging = $factory->createMessaging();

        // Base message configuration
        $baseMessage = \Kreait\Firebase\Messaging\CloudMessage::new()
            ->withNotification(\Kreait\Firebase\Messaging\Notification::create($notification->title, $notification->message))
            ->withData(['click_action' => 'FLUTTER_NOTIFICATION_CLICK'])
            ->withAndroidConfig(\Kreait\Firebase\Messaging\AndroidConfig::fromArray([
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                ],
            ]))
            ->withApnsConfig(\Kreait\Firebase\Messaging\ApnsConfig::fromArray([
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                        // content-available allows background notifications to be processed
                        'content-available' => 1,
                    ]
                ]
            ]));

        if (is_null($notification->user_id)) {
            // Send to topic (all)
            $message = $baseMessage->withChangedTarget('topic', 'healinghand-main-topic');

            try {
                $messaging->send($message);
                return ['success' => true];
            } catch (\Kreait\Firebase\Exception\MessagingException|\Kreait\Firebase\Exception\FirebaseException $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
        } else {
            // Send to specific user's devices
            $user = $notification->user;

            if (!$user || !$user->devices || $user->devices->isEmpty()) {
                return ['success' => false, 'error' => 'User does not have any registered devices.'];
            }

            $messages = [];
            foreach ($user->devices as $device) {
                $messages[] = $baseMessage->withChangedTarget('token', $device->firebase_token);
            }

            try {
                $messaging->sendAll($messages);
                return ['success' => true];
            } catch (\Kreait\Firebase\Exception\MessagingException|\Kreait\Firebase\Exception\FirebaseException $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
        }
    }

    /**
     * Send a custom reminder notification directly from other models.
     * Call this method from another model/controller by passing
     * the user to be notified, and the custom title and message.
     *
     * @param User $user - The user who will receive the notification
     * @param string $title - The notification title
     * @param string $message - The notification message
     */
    public function sendCustomReminder(User $user, string $title, string $message, string $screen = 'appointment')
    {
        if ($user->devices->isEmpty()) {
            \Log::warning("User {$user->id} has no registered devices to send a custom reminder.");
            return;
        }

        $credentialsPath = storage_path('app/firebase/firebase-service-account.json');
        $factory = (new \Kreait\Firebase\Factory())->withServiceAccount($credentialsPath);
        $messaging = $factory->createMessaging();

        $msg = \Kreait\Firebase\Messaging\CloudMessage::new()
            ->withNotification(\Kreait\Firebase\Messaging\Notification::create($title, $message))
            ->withData(['click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'screen' => $screen])
            ->withAndroidConfig(\Kreait\Firebase\Messaging\AndroidConfig::fromArray(['priority' => 'high', 'notification' => ['sound' => 'default']]))
            ->withApnsConfig(\Kreait\Firebase\Messaging\ApnsConfig::fromArray([
                'headers' => ['apns-priority' => '10'],
                'payload' => ['aps' => ['alert' => ['title' => $title, 'body' => $message], 'sound' => 'default', 'badge' => 1]]
            ]));

        foreach ($user->devices as $device) {
            try {
                $messaging->send($msg->withChangedTarget('token', $device->firebase_token));
            } catch (\Exception $e) {
                \Log::error("Failed to send custom reminder to user {$user->id}: " . $e->getMessage());
            }
        }
    }
}
