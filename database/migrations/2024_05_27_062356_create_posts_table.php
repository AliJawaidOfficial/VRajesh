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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('post_id')->nullable();

            $table->string('title');
            $table->text('description')->nullable();
            $table->text('media')->nullable();
            $table->string('media_type')->nullable();

            $table->boolean('on_facebook')->default(0);
            $table->string('facebook_page_id')->nullable();
            $table->text('facebook_page_access_token')->nullable();
            $table->string('facebook_page_name')->nullable();

            $table->boolean('on_instagram')->default(0);
            $table->string('instagram_account_id')->nullable();
            $table->string('instagram_account_name')->nullable();

            $table->boolean('on_linkedin')->default(0);
            $table->string('linkedin_company_id')->nullable();
            $table->string('linkedin_company_name')->nullable();

            $table->dateTime('scheduled_at')->nullable();
            $table->boolean('draft')->default(0);
            $table->boolean('posted')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
