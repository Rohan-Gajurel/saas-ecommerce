<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $user = User::factory(2)->create()->first();

        Business::create([
            'user_id' => 1,
            'business_name' => 'Acme Corporation',
            'business_location' => '123 Main St, Anytown, USA',
            'business_phone' => '555-1234',
            'business_email' => 'info@acmecorp.com',
            'desired_url_name' => 'acme-corp',
            'business_registration_number' => 'ACME12345'
        ]);

        Business::create([
            'user_id' => 2,
            'business_name' => 'Globex Inc.',
            'business_location' => '456 Elm St, Othertown, USA',
            'business_phone' => '555-5678',
            'business_email' => 'info@globex.com',
            'desired_url_name' => 'globex-inc',
            'business_registration_number' => 'GLOBEX67890'
        ]);

    }
}