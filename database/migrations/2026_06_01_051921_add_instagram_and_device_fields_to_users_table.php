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
            $table->string('instagram_id', 100)->nullable();
            $table->string('instagram_username', 255)->nullable();
            $table->text('access_token')->nullable();
            $table->bigInteger('followers_count')->default(0);
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_sync')->nullable();
            $table->string('device_id')->nullable()->unique();
            $table->string('device_secret')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'instagram_id',
                'instagram_username',
                'access_token',
                'followers_count',
                'connected_at',
                'last_sync',
                'device_id',
                'device_secret',
            ]);
        });
    }
};
