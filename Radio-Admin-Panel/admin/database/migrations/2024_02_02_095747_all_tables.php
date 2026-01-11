<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {

            $table->id(); // Auto-incremental primary key
            $table->string('name')->nullable(); // City name
            $table->timestamps(); // Created_at and updated_at columns
        });

        Schema::create('categories', function (Blueprint $table) {

            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();

        });

        Schema::create('radio_stations', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('radio_url')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

        });

        Schema::create('sliders', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('radio_station_id')->nullable();
            $table->foreign('radio_station_id')->references('id')->on('radio_stations')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('sequence')->default(0);
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('radio_station_id');
            $table->foreign('radio_station_id')->references('id')->on('radio_stations')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('date');
            $table->timestamps();

        });

        Schema::create('radio_station_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('radio_station_id')->nullable();
            $table->text('message')->nullable();
            $table->date('date');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {

            $table->id();
            $table->string('type');
            $table->text('message')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        // Insert data into the 'settings' table
        DB::table('settings')->insert([
            [
                'type' => 'app_name',
                'message' => 'Radio Online App',
            ],
            [
                'type' => 'timezone',
                'message' => 'Asia/Kolkata',
            ],
            [
                'type' => 'city_mode',
                'message' => '1',
            ],
            [
                'type' => 'maintenance_mode',
                'message' => '0',
            ],
            [
                'type' => 'primarycolor',
                'message' => '#b0506a',
            ],
            [
                'type' => 'backgroundcolor',
                'message' => '#f7f7f7',
            ],
            [
                'type' => 'darkprimarycolor',
                'message' => '#ef8a83',
            ],
            [
                'type' => 'darkbackgroundcolor',
                'message' => '#171616',
            ],
            [
                'type' => 'system_version',
                'message' => '2.0.0',
            ],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('radio_stations');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('radio_station_reports');
        Schema::dropIfExists('settings');
    }
};
