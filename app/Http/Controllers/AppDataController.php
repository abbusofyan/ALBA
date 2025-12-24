<?php

namespace App\Http\Controllers;

use App\Models\AppData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AppDataController extends Controller
{
    public function __construct()
    {
//        $this->authorizeResource(AppData::class, 'app_data');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->search;

        $order = $request->order ?? 'key';
        $by = $request->by ?? 'asc';
        $paginate = $request->paginate ?? 10;

        $filters = $request->only(['search', 'order', 'by', 'paginate']);

        $appData = AppData::when($search, function ($query) use ($search) {
            $query->where('key', 'LIKE', "%{$search}%")
                ->orWhere('value', 'LIKE', "%{$search}%");
        })
            ->orderBy($order, $by)
            ->paginate($paginate)
            ->appends($filters);

        return Inertia::render('AppData/Index', [
            'appData' => $appData,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('AppData/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation rules based on type
        $rules = [
            'key' => 'required|string|unique:app_data',
            'type' => 'required|string',
        ];

        if ($request->type == 'Text') {
            $rules['value'] = 'required|string';
        } elseif ($request->type == 'Image') {
            $rules['file'] = 'required|file|image|max:2048'; // Max size 2MB
        } elseif ($request->type == 'Repeater Text') {
            $rules['values'] = 'required|array|min:1';
            $rules['values.*'] = 'required|string';
        } elseif ($request->type == 'Repeater Image') {
            $rules['files'] = 'required|array|min:1';
            $rules['files.*'] = 'required|file|image|max:2048';
        }

        $validated = $request->validate($rules);

        $appData = new AppData();
        $appData->key = $validated['key'];
        $appData->type = $validated['type'];

        if ($appData->type == 'Text') {
            $appData->value = $validated['value'];
        } elseif ($appData->type == 'Image') {
            $path = $request->file('file')->store("documents/app-data/{$appData->key}", 'public');
            $appData->value = $path;
        } elseif ($appData->type == 'Repeater Text') {
            $appData->value = json_encode($validated['values']);
        } elseif ($appData->type == 'Repeater Image') {
            $paths = [];
            foreach ($request->file('files') as $file) {
                $paths[] = $file->store("documents/app-data/{$appData->key}", 'public');
            }
            $appData->value = json_encode($paths);
        }

        $appData->save();

        return redirect('/app-data')->with('created', 'App Data created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppData $app_data): Response
    {
        return Inertia::render('AppData/Form', [
            'appData' => $app_data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppData $app_data)
    {
        // Validation rules based on type
        $rules = [
            'key' => 'required|string|unique:app_data,key,' . $app_data->id,
            'type' => 'required|string',
        ];

        if ($request->type == 'Text') {
            $rules['value'] = 'required|string';
        } elseif ($request->type == 'Image') {
            $rules['file'] = 'nullable|file|image|max:2048';
        } elseif ($request->type == 'Repeater Text') {
            $rules['values'] = 'required|array|min:1';
            $rules['values.*'] = 'required|string';
        } elseif ($request->type == 'Repeater Image') {
            $rules['files'] = 'nullable|array|min:1';
            $rules['files.*'] = 'nullable|file|image|max:2048';
        }

        $validated = $request->validate($rules);

        $app_data->key = $validated['key'];
        $app_data->type = $validated['type'];

        if ($app_data->type == 'Text') {
            $app_data->value = $validated['value'];
        } elseif ($app_data->type == 'Image') {
            if ($request->hasFile('file')) {
                // Delete old file
                if (!empty($app_data->value) && Storage::disk('public')->exists($app_data->value)) {
                    Storage::disk('public')->delete($app_data->value);
                }
                $path = $request->file('file')->store("documents/app-data/{$app_data->key}", 'public');
                $app_data->value = $path;
            }
        } elseif ($app_data->type == 'Repeater Text') {
            $app_data->value = json_encode($validated['values']);
        } elseif ($app_data->type == 'Repeater Image') {
            if ($request->hasFile('files')) {
                // Delete old files
                if (!empty($app_data->value)) {
                    $oldPaths = json_decode($app_data->value, true);
                    foreach ($oldPaths as $oldPath) {
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
                $paths = [];
                foreach ($request->file('files') as $file) {
                    $paths[] = $file->store("documents/app-data/{$app_data->key}", 'public');
                }
                $app_data->value = json_encode($paths);
            }
        }

        $app_data->save();

        return redirect('/app-data')->with('updated', 'App Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppData $app_data)
    {
        // Delete associated files based on type
        if ($app_data->type == 'Image' && !empty($app_data->value)) {
            if (Storage::disk('public')->exists($app_data->value)) {
                Storage::disk('public')->delete($app_data->value);
            }
        } elseif ($app_data->type == 'Repeater Image' && !empty($app_data->value)) {
            $paths = json_decode($app_data->value, true);
            foreach ($paths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $app_data->delete();

        return back()->with('deleted', 'App Data deleted successfully');
    }
}
