<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Menu extends Model
{
    protected $fillable = ['name', 'link', 'order', 'parent_id', 'image', "menu_key", "is_heading"];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }


    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'menu_roles', 'menu_id', 'role_id');
    }
}
