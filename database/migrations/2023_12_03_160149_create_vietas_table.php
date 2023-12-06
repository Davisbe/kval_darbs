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
        Schema::create('vietas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 300)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->decimal('garums', 9, 6);
            $table->decimal('platums', 8, 6);
            $table->unsignedInteger('pielaujama_kluda');
            $table->unsignedTinyInteger('sarezgitiba');
            $table->text('picture');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vietas');
    }
};
