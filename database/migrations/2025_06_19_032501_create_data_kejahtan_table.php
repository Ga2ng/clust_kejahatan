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
        Schema::create('data_kejahatan', function (Blueprint $table) {
            $table->id(); // auto increment primary key
            $table->string('tahun', 4);
            $table->string('bulan', 10);
            $table->integer('curas');
            $table->integer('curat');
            $table->integer('curanmor');
            $table->integer('anirat');
            $table->integer('judi');
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kejahatan');
    }
};