<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follower_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instagram_account_id')->constrained()->onDelete('cascade');
            $table->bigInteger('followers_count');
            $table->timestamp('recorded_at');

            $table->index(['instagram_account_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follower_logs');
    }
};
