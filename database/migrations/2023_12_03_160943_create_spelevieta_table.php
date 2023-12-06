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
        Schema::create('spelevieta', function (Blueprint $table) {
            $table->unsignedBigInteger('spele_id');
            $table->unsignedBigInteger('vieta_id');
            $table->primary(['spele_id', 'vieta_id']);
            $table->timestamps();
            $table->foreign('spele_id')->references('id')->on('speles')->cascadeOnDelete();
            $table->foreign('vieta_id')->references('id')->on('vietas')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spelevieta');
    }
};
