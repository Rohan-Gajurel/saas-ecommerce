<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CartSansarModel extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('business', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('business_id', Auth::user()->business_id);
            }
        });
    }

}