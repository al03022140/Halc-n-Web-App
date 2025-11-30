<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        SystemSetting::setValue('invoice_prefix', SystemSetting::value('invoice_prefix', 'FAC-'));
        SystemSetting::setValue('invoice_next_number', SystemSetting::value('invoice_next_number', 1));
        SystemSetting::setValue('require_fiscal_data', SystemSetting::value('require_fiscal_data', true));
        SystemSetting::setValue('require_delivery_address', SystemSetting::value('require_delivery_address', true));
        SystemSetting::setValue('purchasing_alert_email', SystemSetting::value('purchasing_alert_email', config('mail.from.address')));
        SystemSetting::setValue('slack_stock_webhook', SystemSetting::value('slack_stock_webhook', ''));
    }
}
