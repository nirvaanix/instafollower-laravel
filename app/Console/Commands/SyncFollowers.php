<?php

namespace App\Console\Commands;

use App\Models\FollowerLog;
use App\Models\InstagramAccount;
use App\Services\InstagramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncFollowers extends Command
{
    protected $signature = 'instagram:sync-followers';
    protected $description = 'Sync follower counts for all active Instagram accounts from the Graph API';

    public function handle(InstagramService $instagram): int
    {
        $accounts = InstagramAccount::where('is_active', true)
            ->whereNotNull('access_token')
            ->where('access_token', '!=', '')
            ->get();

        $this->info("Syncing {$accounts->count()} active Instagram accounts...");

        $synced = 0;
        $failed = 0;

        foreach ($accounts as $account) {
            try {
                // Skip if token is expired
                if ($account->isTokenExpired()) {
                    $this->warn("Skipping @{$account->username} — token expired");
                    $failed++;
                    continue;
                }

                $profile = $instagram->getInstagramProfile($account->instagram_id, $account->access_token);

                if (!$profile) {
                    $this->warn("Failed to fetch profile for @{$account->username}");
                    $failed++;
                    continue;
                }

                $oldCount = $account->followers_count;
                $newCount = $profile['followers_count'] ?? 0;

                $account->update([
                    'followers_count' => $newCount,
                    'media_count'     => $profile['media_count'] ?? $account->media_count,
                    'bio'             => $profile['biography'] ?? $account->bio,
                    'last_sync'       => now(),
                ]);

                // Log follower snapshot
                FollowerLog::create([
                    'instagram_account_id' => $account->id,
                    'followers_count'      => $newCount,
                    'recorded_at'          => now(),
                ]);

                $diff = $newCount - $oldCount;
                $diffStr = $diff >= 0 ? "+{$diff}" : (string) $diff;
                $this->info("  @{$account->username}: {$newCount} followers ({$diffStr})");
                $synced++;

            } catch (\Exception $e) {
                Log::error("Sync failed for @{$account->username}", ['error' => $e->getMessage()]);
                $this->error("  @{$account->username}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("Done. Synced: {$synced}, Failed: {$failed}");

        return self::SUCCESS;
    }
}
