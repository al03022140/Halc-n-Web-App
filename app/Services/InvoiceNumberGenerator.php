<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class InvoiceNumberGenerator
{
    public function generate(): string
    {
        return DB::transaction(function () {
            $prefix = SystemSetting::value('invoice_prefix', 'FAC-');
            $next = (int) SystemSetting::value('invoice_next_number', 1);

            if ($next < 1) {
                $next = 1;
            }

            $formatted = $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);

            SystemSetting::setValue('invoice_next_number', $next + 1);

            return $formatted;
        });
    }
}
