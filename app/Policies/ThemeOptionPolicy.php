<?php

namespace App\Policies;

use App\Models\ThemeOption;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ThemeOptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-theme-option');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ThemeOption $themeOption): bool
    {
        return $user->can('view-theme-option');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-theme-option');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ThemeOption $themeOption): bool
    {
        return $user->can('update-theme-option');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ThemeOption $themeOption): bool
    {
        return $user->can('delete-theme-option');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ThemeOption $themeOption): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ThemeOption $themeOption): bool
    {
        return false;
    }
}
