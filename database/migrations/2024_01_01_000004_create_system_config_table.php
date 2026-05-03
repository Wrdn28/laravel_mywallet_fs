<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_config', function (Blueprint $table) {
            $table->id();
            $table->string('config_key')->unique();
            $table->string('config_value')->nullable();
            $table->timestamps();
        });

        // Default values
        DB::table('system_config')->insert([
            ['config_key' => 'maintenance_mode', 'config_value' => '0'],
            ['config_key' => 'app_name',         'config_value' => 'DOMPETKU'],
            ['config_key' => 'admin_email',      'config_value' => 'admin@dompetku.com'],
            ['config_key' => 'system_version',   'config_value' => 'v1.0.0'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_config');
    }
};
