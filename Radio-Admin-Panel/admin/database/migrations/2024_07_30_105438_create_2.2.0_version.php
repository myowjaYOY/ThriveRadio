<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Correct deletion of settings
        DB::table('settings')
            ->where('type', 'maintenance_mode')
            ->orWhere('type', 'dark_primary_color')
            ->orWhere('type', 'dark_background_color')
            ->delete();

        // Insert multiple records
        DB::table('settings')->insert([
            [
                'type' => 'app_maintenance',
                'message' => '0',
            ],
            [
                'type' => 'app_version',
                'message' => '1.0.0',
            ],
            [
                'type' => 'ios_app_version',
                'message' => '1.0.0',
            ]
        ]);
    }

    public function down(): void
    {
        // Correct deletion of new settings
        DB::table('settings')
            ->where('type', 'app_maintenance')
            ->orWhere('type', 'app_version')
            ->orWhere('type', 'ios_app_version')
            ->delete();

        // Re-insert the old settings if necessary
        DB::table('settings')->insert([
            [
                'type' => 'maintenance_mode',
                'message' => '0',
            ],
            [
                'type' => 'dark_primary_color',
                'message' => null,
            ],
            [
                'type' => 'dark_background_color',
                'message' => null,
            ]
        ]);
    }
};
