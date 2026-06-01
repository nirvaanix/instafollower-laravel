<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instagram_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('instagram_id', 100)->unique();
            $table->string('username', 255)->unique();
            $table->text('profile_picture_url')->nullable();
            $table->text('bio')->nullable();
            $table->bigInteger('followers_count')->default(0);
            $table->bigInteger('media_count')->default(0);
            $table->text('access_token');
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_sync')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('user_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instagram_accounts');
    }
};
