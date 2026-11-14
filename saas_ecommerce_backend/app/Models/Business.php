<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'business_name',
        'desired_url_name',
        'business_location',
        'business_registration_number',
        'business_email',
        'business_phone',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Suspend the business (halt all operations).
     */
    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    /**
     * Reactivate a suspended business.
     */
    public function reactivate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Check if business is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if business is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
