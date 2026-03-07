<?php

namespace Modules\User\App\Console;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserCleanCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = "user:clean";

    /**
     * @var string
     */
    protected $description = "Clean expired password reset tokens and stale unverified users";

    /**
     * @return int
     */
    public function handle(): int
    {
        DB::table("password_reset_tokens")
            ->where("created_at", "<", now()->subDay())
            ->delete();

        User::query()
            ->whereNull("email_verified_at")
            ->where("created_at", "<", now()->subDays(7))
            ->delete();

        $this->info("User cleanup completed.");

        return self::SUCCESS;
    }
}
