<?php

namespace App\Policies;

use App\Models\Assets;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssetsPolicy
{
  // function before(): bool
  // {
  // return true;
  // # return Response::denyWithStatus(404);
  // # return Response::deny('Custom message');
  // }
  /**
   * Determine whether the user can view any models.
   */
  function viewAny(User $user): bool
  {
    return true;
  }

  /**
   * Determine whether the user can view the model.
   */
  function view(User $user, Assets $assets): bool
  {
    return true;
  }

  /**
   * Determine whether the user can create models.
   */
  function create(User $user): bool
  {
    return true;
  }

  /**
   * Determine whether the user can update the model.
   */
  function update(User $user, Assets $assets): bool
  {
    return true;
  }

  /**
   * Determine whether the user can delete the model.
   */
  function delete(User $user, Assets $assets): bool
  {
    return true;
  }

  /**
   * Determine whether the user can restore the model.
   */
  function restore(User $user, Assets $assets): bool
  {
    return true;
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  function forceDelete(User $user, Assets $assets): bool
  {
    return true;
  }
}
