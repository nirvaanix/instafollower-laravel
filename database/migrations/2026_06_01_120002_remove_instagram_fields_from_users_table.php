<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove Instagram-specific fields from users table.
     * All IG data now lives in instagram_accounts table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'instagram_id', 'instagram_username', 'profile_picture_url',
                'bio', 'access_token', 'followers_count', 'media_count',
                'connected_at', 'last_sync',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Also drop legacy device columns if they exist
            if (Schema::hasColumn('users', 'device_id')) {
                $table->dropUnique(['device_id']);
                $table->dropColumn('device_id');
            }
            if (Schema::hasColumn('users', 'device_secret')) {
                $table->dropColumn('device_secret');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('instagram_id', 100)->nullable();
            $table->string('instagram_username', 255)->nullable();
            $table->text('profile_picture_url')->nullable();
            $table->text('bio')->nullable();
            $table->text('access_token')->nullable();
            $table->bigInteger('followers_count')->default(0);
            $table->bigInteger('media_count')->default(0);
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_sync')->nullable();
        });
    }
};
