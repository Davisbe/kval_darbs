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
        Schema::create('sazina', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spele_id');
            $table->unsignedBigInteger('user_id');
            $table->string('text', 300)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->timestamps();
            $table->foreign('spele_id')->references('id')->on('speles')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sazina');
    }
};
