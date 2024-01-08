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
        Schema::create('atrastavieta', function (Blueprint $table) {
            $table->unsignedBigInteger('grupa_id');
            $table->unsignedBigInteger('vieta_id');
            $table->primary(['grupa_id', 'vieta_id']);
            $table->text('picture')->nullable();
            $table->timestamps();
            $table->foreign('grupa_id')->references('id')->on('grupas')->cascadeOnDelete();
            $table->foreign('vieta_id')->references('id')->on('vietas')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atrastavieta');
    }
};
