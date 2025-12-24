<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Http;

class Helper
{
    public static function getMenuAdminPanel()
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();
        $roleId = $user->roles()->where('name', $role)->pluck('id')->first();

        $menus = Menu::with(['roles', 'children.roles'])
            ->whereNull('parent_id')
            ->whereHas('roles', function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })
            ->orderBy('order', 'ASC')
            ->get();

        $menus->each(function ($menu) use ($roleId) {
            $menu->setRelation(
                'children',
                $menu->children->filter(function ($child) use ($roleId) {
                    return $child->roles->pluck('id')->contains($roleId);
                })
            );
        });

        $formattedMenus = $menus->map(function ($menu) {
            return self::formatMenu($menu);
        });

        return $formattedMenus->toArray();
    }


    private static function formatMenu($menu)
    {
        $children = $menu->children->map(function ($child) {
            return self::formatMenu($child);
        });

        return array_filter([
            'name' => $menu->name,
            'link' => $menu->link ?? null,
            'heading' => $menu->is_heading ? $menu->name : null,
            'icon' => !empty($menu->image) ? $menu->image : null,
            'children' => $children->isNotEmpty() ? $children->toArray() : null,
        ]);
    }

	public static function singaporeOneMapAPI($search) {
		$response = Http::get('https://www.onemap.gov.sg/api/common/elastic/search', [
            'searchVal' => $search,
            'returnGeom' => 'Y',
            'getAddrDetails' => 'Y',
            'pageNum' => 1
        ]);
		return $response;
	}

	public static function getRawQueryParam(string $key): ?string
	{
		$queryString = $_SERVER['QUERY_STRING'] ?? '';

		// Match the key and capture its value
		if (preg_match('/(?:^|&)' . preg_quote($key, '/') . '=([^&]*)/', $queryString, $matches)) {
			// Use rawurldecode (decodes %xx but keeps '+')
			return rawurldecode($matches[1]);
		}

		return null;
	}

}
