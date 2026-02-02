<?php

namespace App\Policies;

use App\Models\Diary;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiaryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_diary');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Diary $diary): bool
    {
        return $user->can('view_diary');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_diary');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Diary $diary): bool
    {
        return $user->can('update_diary');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Diary $diary): bool
    {
        return $user->can('delete_diary');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_diary');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Diary $diary): bool
    {
        return $user->can('force_delete_diary');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_diary');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Diary $diary): bool
    {
        return $user->can('restore_diary');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_diary');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Diary $diary): bool
    {
        return $user->can('replicate_diary');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_diary');
    }
}
