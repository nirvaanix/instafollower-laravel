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
            // Add profile_picture_url if it doesn't exist
            if (!Schema::hasColumn('users', 'profile_picture_url')) {
                $table->text('profile_picture_url')->nullable()->after('instagram_username');
            }
            // Add bio if it doesn't exist
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('profile_picture_url');
            }
            // Add media_count if it doesn't exist
            if (!Schema::hasColumn('users', 'media_count')) {
                $table->bigInteger('media_count')->default(0)->after('followers_count');
            }

            // Drop device columns if they exist
            if (Schema::hasColumn('users', 'device_id')) {
                $table->dropUnique(['device_id']);
                $table->dropColumn('device_id');
            }
            if (Schema::hasColumn('users', 'device_secret')) {
                $table->dropColumn('device_secret');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_picture_url')) {
                $table->dropColumn('profile_picture_url');
            }
            if (Schema::hasColumn('users', 'bio')) {
                $table->dropColumn('bio');
            }
            if (Schema::hasColumn('users', 'media_count')) {
                $table->dropColumn('media_count');
            }
            if (!Schema::hasColumn('users', 'device_id')) {
                $table->string('device_id')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'device_secret')) {
                $table->string('device_secret')->nullable();
            }
        });
    }
};
