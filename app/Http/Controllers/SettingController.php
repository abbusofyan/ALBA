<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{

	public function __construct()
	{
		$this->middleware('permission:view-setting')->only(['index', 'show']);
		$this->middleware('permission:update-setting')->only(['storePoints']);
	}

    public function index()
    {
		$setting = Setting::first();

        return Inertia::render('Setting/Index', [
            'setting' => $setting,
        ]);
    }

    public function storePoints(Request $request)
    {
        $request->validate([
            'user_max_daily_reward' => 'required|numeric',
			'user_max_monthly_redemption' => 'required|numeric',
        ]);

		$setting = Setting::updateOrCreate(
		    ['id' => 1],
		    [
		        'user_max_daily_reward' => $request->user_max_daily_reward,
		        'user_max_monthly_redemption' => $request->user_max_monthly_redemption
		    ]
		);

        return redirect()->back()->with('created', 'Setting saved succesfully.');
    }
}
