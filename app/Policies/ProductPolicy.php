<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\JsonResponse;

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
    public function create(User $user): Response
    {
        return auth()->id() === $user->id
            ? Response::allow()
            : Response::deny('You are not authorized for this action.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Product $product
     *
     * @return boolean
     */
    public function update(User $user, Product $product): Response
    {
        return $product->user()->is($user)
            ? Response::allow()
            : Response::deny('You do not own this product.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Product $product
     *
     * @return boolean
     */
    public function delete(User $user, Product $product): Response
    {
        return $product->user()->is($user)
            ? Response::allow()
            : Response::deny('You do not own this product.');
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
