<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'invoice_prefix' => SystemSetting::value('invoice_prefix', 'FAC-'),
            'invoice_next_number' => SystemSetting::value('invoice_next_number', 1),
            'require_fiscal_data' => SystemSetting::bool('require_fiscal_data', true),
            'require_delivery_address' => SystemSetting::bool('require_delivery_address', true),
            'purchasing_alert_email' => SystemSetting::value('purchasing_alert_email', config('mail.from.address')),
            'slack_stock_webhook' => SystemSetting::value('slack_stock_webhook', ''),
        ];

        return view('settings.system', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'invoice_prefix' => 'required|string|max:10',
            'invoice_next_number' => 'required|integer|min:1',
            'require_fiscal_data' => 'nullable|boolean',
            'require_delivery_address' => 'nullable|boolean',
            'purchasing_alert_email' => 'nullable|email',
            'slack_stock_webhook' => 'nullable|url',
        ]);

        SystemSetting::setValue('invoice_prefix', $data['invoice_prefix']);
        SystemSetting::setValue('invoice_next_number', $data['invoice_next_number']);
        SystemSetting::setValue('require_fiscal_data', $request->boolean('require_fiscal_data'));
        SystemSetting::setValue('require_delivery_address', $request->boolean('require_delivery_address'));
        SystemSetting::setValue('purchasing_alert_email', $data['purchasing_alert_email'] ?? null);
        SystemSetting::setValue('slack_stock_webhook', $data['slack_stock_webhook'] ?? null);

        return redirect()->route('settings.system')->with('success', 'Parámetros actualizados correctamente.');
    }
}
