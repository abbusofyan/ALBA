<?php
namespace App\Http\Middleware;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash'           => [
                'created'    => fn()    => $request->session()->get('created'),
                'updated'    => fn()    => $request->session()->get('updated'),
                'deleted'    => fn()    => $request->session()->get('deleted'),
                'error'      => fn()      => $request->session()->get('error'),
                'signed'     => fn()     => $request->session()->get('signed'),
                'canceled'   => fn()   => $request->session()->get('canceled'),
                'status'     => fn()     => $request->session()->get('status'),
                'registered' => fn() => $request->session()->get('registered'),
                'success'    => fn()    => $request->session()->get('success'),
            ],
            'ziggy'           => fn()           => [
                 ...(new Ziggy)->toArray(),
                'location' => $request->url(),
                'query'    => $request->query(),
            ],
            'auth'            => Auth::user() ? [
                'user' => [
                    'name'        => Auth::user()->name,
                    'username'    => Auth::user()->username,
                    'avatar'      => Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : '',
                    'background'  => Auth::user()->background,
                    'darkmode'    => Auth::user()->dark_mode_text,
                    'status'      => Auth::user()->status_text,
                    'role'        => Auth::user()->getRoleNames()->first(),
                    'permissions' => Auth::user()->getAllPermissions()->pluck('name'),
                ],
            ] : null,
            'auth.user.menus' => function () {
                if (Auth::check() && Auth::id()) {
                    $user = Auth::user();
                    if ($user->getRoleNames()->isNotEmpty()) {
                        return Helper::getMenuAdminPanel();
                    }
                }
                return [];
            },

            'base_url'        => url('/'),
        ]);
    }
}
