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
        Schema::create('runners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('country')->nullable();
            $table->enum('gender', ['men', 'women']);
            $table->integer('utmb_index_20k')->default(0);
            $table->integer('utmb_index_50k')->default(0);
            $table->integer('utmb_index_100k')->default(0);
            $table->integer('utmb_index_100m')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('runners');
    }
};
