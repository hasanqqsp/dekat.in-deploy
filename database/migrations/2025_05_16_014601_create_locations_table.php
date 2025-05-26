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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('alamat_lengkap')->nullable();
            $table->json('coords')->nullable();
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->time('open_hour')->nullable();
            $table->time('close_hour')->nullable();
            $table->integer('start_price')->nullable();
            $table->integer('end_price')->nullable();
            $table->foreignId('contributor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
