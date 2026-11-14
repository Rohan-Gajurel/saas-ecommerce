<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed a default superadmin user.
     */
    public function run(): void
{
    $email = env('SUPERADMIN_EMAIL');

    if (!$email || !env('SUPERADMIN_PASSWORD')) {
        throw new \Exception('Superadmin credentials missing in .env');
    }

    $user = User::query()->updateOrCreate(
        ['email' => $email],
        [
            'name' => env('SUPERADMIN_NAME'),
            'full_name' => env('SUPERADMIN_NAME'),
            'address' => env('SUPERADMIN_ADDRESS'),
            'phone_no' => env('SUPERADMIN_PHONE'),
            'password' => Hash::make(env('SUPERADMIN_PASSWORD')),
        ]
    );

    Role::findOrCreate('Super Admin');
    $user->syncRoles(['Super Admin']);
}
}
