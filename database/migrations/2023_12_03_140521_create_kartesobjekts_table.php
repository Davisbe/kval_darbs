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
        Schema::create('kartesobjekts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karte_id')->nullable();
            $table->text('geojson');
            $table->timestamps();
            $table->foreign('karte_id')->references('id')->on('kartes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartesobjekts');
    }
};
