<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SystemSetting;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class InventoryAlertService
{
    public function notifyIfBelowThreshold(Product $product): void
    {
        if ($product->reorder_level === null) {
            return;
        }

        if ($product->stock > $product->reorder_level) {
            return;
        }

        $cacheKey = 'low-stock-alert:' . $product->id . ':' . $product->stock;
        if (Cache::has($cacheKey)) {
            return;
        }

        $email = SystemSetting::value('purchasing_alert_email');
        $webhook = SystemSetting::value('slack_stock_webhook');

        if ($email) {
            Notification::route('mail', $email)->notify(new LowStockNotification($product, ['mail']));
        }

        if ($webhook) {
            Notification::route('slack', $webhook)->notify(new LowStockNotification($product, ['slack']));
        }

        Cache::put($cacheKey, true, now()->addMinutes(30));
    }
}
