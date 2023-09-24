<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

/**
 * This class is intended to be used for authorization
 * of users' permissions for certain actions.
 */
class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     *
     * @return boolean
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Product $product
     *
     * @return boolean
     */
    public function view(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     *
     * @return boolean
     */
    public function create(User $user): bool
    {
        return auth()->id() === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Product $product
     *
     * @return boolean
     */
    public function update(User $user, Product $product): bool
    {
        return $product->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Product $product
     *
     * @return boolean
     */
    public function delete(User $user, Product $product): bool
    {
        return $product->user()->is($user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Product $product
     *
     * @return boolean
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Product $product
     *
     * @return boolean
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}
