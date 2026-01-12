<?php

namespace App\Policies;

use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both admin and venue_owner can view categories
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EventCategory $eventCategory): bool
    {
        return true; // Both admin and venue_owner can view category details
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admin can create categories
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EventCategory $eventCategory): bool
    {
        return $user->isAdmin(); // Only admin can update categories
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EventCategory $eventCategory): bool
    {
        return $user->isAdmin(); // Only admin can delete categories
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EventCategory $eventCategory): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EventCategory $eventCategory): bool
    {
        return $user->isAdmin();
    }
}
