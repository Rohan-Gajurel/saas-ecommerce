<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable()->after('name');
            }

            if (! Schema::hasColumn('users', 'phone_no')) {
                $table->string('phone_no', 20)->nullable()->after('address');
            }
        });

        if (Schema::hasColumn('users', 'contact_no') && Schema::hasColumn('users', 'phone_no')) {
            DB::table('users')
                ->whereNull('phone_no')
                ->whereNotNull('contact_no')
                ->update(['phone_no' => DB::raw('contact_no')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone_no')) {
                $table->dropColumn('phone_no');
            }

            if (Schema::hasColumn('users', 'full_name')) {
                $table->dropColumn('full_name');
            }
        });
    }
};
