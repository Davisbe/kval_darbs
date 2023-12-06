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
        Schema::create('grupas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spele_id')->nullable();
            $table->timestamps();
            $table->foreign('spele_id')->references('id')->on('speles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupas');
    }
};
