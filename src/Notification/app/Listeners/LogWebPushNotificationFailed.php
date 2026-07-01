<?php

namespace Modules\Notification\App\Listeners;

use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\Events\NotificationFailed;

class LogWebPushNotificationFailed
{
    /**
     * @param \NotificationChannels\WebPush\Events\NotificationFailed $event
     * @return void
     */
    public function handle(NotificationFailed $event): void
    {
        $report = $event->report;
        $response = $report->getResponse();

        Log::warning("Web Push delivery failed", [
            "endpoint" => $event->subscription->endpoint ?? null,
            "expired" => $report->isSubscriptionExpired(),
            "reason" => $report->getReason(),
            "status" => $response !== null ? $response->getStatusCode() : null,
        ]);

        if (! $report->isSubscriptionExpired()) {
            return;
        }

        try {
            $event->subscription?->delete();
        } catch (\Throwable $thrower) {
            Log::warning("Web Push subscription cleanup failed", [
                "endpoint" => $event->subscription->endpoint ?? null,
                "error" => $thrower->getMessage(),
            ]);
        }
    }
}
