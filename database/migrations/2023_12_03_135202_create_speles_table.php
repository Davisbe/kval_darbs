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
        Schema::create('speles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karte_id')->nullable();
            $table->string('name', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('description', 2000)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->useCurrent();
            $table->text('picture')->nullable();
            $table->timestamps();
            $table->foreign('karte_id')->references('id')->on('kartes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speles');
    }
};
