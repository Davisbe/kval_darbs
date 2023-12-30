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
        Schema::create('lietotajsgrupa', function (Blueprint $table) {
            $table->unsignedBigInteger('grupa_id');
            $table->unsignedBigInteger('user_id');
            $table->primary(['grupa_id', 'user_id']);
            $table->boolean('uzaicinats')->default(1);
            $table->boolean('apstiprinats')->default(-1);
            $table->boolean('active')->default(-1);
            $table->timestamps();
            $table->foreign('grupa_id')->references('id')->on('grupas')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lietotajsgrupa');
    }
};
