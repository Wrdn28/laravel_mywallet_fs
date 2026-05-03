<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rencana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->enum('tipe', ['tabungan', 'anggaran']);
            $table->string('kategori')->nullable();
            $table->unsignedBigInteger('target');
            $table->unsignedBigInteger('terkumpul')->default(0);
            $table->date('deadline')->nullable();
            $table->string('warna')->default('#a78bfa');
            $table->string('icon')->default('savings');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rencana');
    }
};
