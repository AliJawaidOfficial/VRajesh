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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->text('meta_access_token')->nullable();
            $table->text('meta_avatar')->nullable();
            $table->string('meta_name')->nullable();
            $table->string('meta_email')->nullable();

            $table->text('linkedin_access_token')->nullable();
            $table->text('linkedin_community_access_token')->nullable();

            $table->text('linkedin_urn')->nullable();
            $table->text('linkedin_avatar')->nullable();
            $table->string('linkedin_name')->nullable();
            $table->string('linkedin_email')->nullable();

            $table->text('google_access_token')->nullable();
            $table->text('google_refresh_access_token')->nullable();
            $table->timestamp('google_token_expires_at')->nullable();
            $table->text('google_avatar')->nullable();
            $table->string('google_name')->nullable();
            $table->string('google_email')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
