<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAnyProduct(User $user): bool
    {
        return $user->can('products.view');
    }

    public function viewProduct(User $user, Product $product): bool
    {
        return $user->business_id === $product->business_id && ($user->can('products.view'));
    }

    public function createProduct(User $user): bool
    {
        return $user->can('products.create');
    }

    public function updateProduct(User $user, Product $product): bool
    {
        return $user->business_id === $product->business_id && ($user->can('products.update'));
    }

    public function deleteProduct(User $user, Product $product): bool
    {
        return $user->business_id === $product->business_id && ($user->can('products.delete'));
    }
}