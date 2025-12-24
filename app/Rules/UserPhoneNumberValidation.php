<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserPhoneNumberValidation implements Rule
{
    protected ?int $userId;

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId;
    }

    public function passes($attribute, $value): bool
    {
        $publicRoleId = Role::where('name', 'Public')->value('id');

        if (!$publicRoleId) {
            return true;
        }

        $query = DB::table('users')
            ->join('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                     ->where('model_has_roles.model_type', '=', 'App\\Models\\User');
            })
            ->where('model_has_roles.role_id', $publicRoleId)
            ->where('users.phone', $value)
            ->whereNull('users.deleted_at');

        if ($this->userId) {
            $query->where('users.id', '!=', $this->userId);
        }

        return !$query->exists();
    }

    public function message(): string
    {
        return 'This phone number is already taken by another user.';
    }
}
