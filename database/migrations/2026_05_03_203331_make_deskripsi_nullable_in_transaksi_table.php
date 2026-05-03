<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('deskripsi')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Isi dulu yang null sebelum revert ke NOT NULL
            \DB::table('transaksi')->whereNull('deskripsi')->update(['deskripsi' => '']);
            $table->string('deskripsi')->nullable(false)->change();
        });
    }
};
