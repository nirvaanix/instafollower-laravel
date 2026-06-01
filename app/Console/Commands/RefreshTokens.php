<?php

namespace App\Console\Commands;

use App\Models\InstagramAccount;
use App\Services\InstagramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshTokens extends Command
{
    protected $signature = 'instagram:refresh-tokens';
    protected $description = 'Refresh long-lived tokens that are expiring within 7 days';

    public function handle(InstagramService $instagram): int
    {
        $accounts = InstagramAccount::where('is_active', true)
            ->whereNotNull('access_token')
            ->where('access_token', '!=', '')
            ->where(function ($query) {
                $query->whereNull('token_expires_at')
                      ->orWhere('token_expires_at', '<=', now()->addDays(7));
            })
            ->get();

        $this->info("Found {$accounts->count()} tokens to refresh...");

        $refreshed = 0;
        $failed = 0;

        foreach ($accounts as $account) {
            try {
                $newToken = $instagram->getLongLivedToken($account->access_token);

                if ($newToken) {
                    $account->update([
                        'access_token'     => $newToken,
                        'token_expires_at' => now()->addDays(60),
                    ]);
                    $this->info("  @{$account->username}: token refreshed");
                    $refreshed++;
                } else {
                    $this->warn("  @{$account->username}: refresh failed — user may need to re-auth");
                    $failed++;
                }
            } catch (\Exception $e) {
                Log::error("Token refresh failed for @{$account->username}", ['error' => $e->getMessage()]);
                $this->error("  @{$account->username}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("Done. Refreshed: {$refreshed}, Failed: {$failed}");

        return self::SUCCESS;
    }
}
