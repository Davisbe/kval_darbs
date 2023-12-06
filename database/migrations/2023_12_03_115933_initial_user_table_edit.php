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
        Schema::table('users', function (Blueprint $table) {
            $table->string('old_email', 255)->nullable();
            $table->text('profile_picture')->nullable();
            $table->boolean('hide_profile_info')->default(false);
            $table->text('google_oauth')->nullable();
            $table->unsignedInteger('places_found')->default(0);
            $table->boolean('suspended_profile')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('old_email');
            $table->dropColumn('profile_picture');
            $table->dropColumn('hide_profile_info');
            $table->dropColumn('google_oauth');
            $table->dropColumn('places_found');
            $table->dropColumn('suspended_profile');
        });
    }
};
