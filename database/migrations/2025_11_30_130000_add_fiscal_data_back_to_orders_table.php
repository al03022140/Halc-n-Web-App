<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'fiscal_data')) {
                $table->string('fiscal_data')->nullable()->after('customer_number');
            }
        });

        if (Schema::hasColumn('orders', 'client_id') && Schema::hasColumn('clients', 'fiscal_data')) {
            DB::statement('UPDATE orders SET fiscal_data = (
                SELECT fiscal_data FROM clients WHERE clients.id = orders.client_id
            ) WHERE fiscal_data IS NULL');
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'fiscal_data')) {
                $table->dropColumn('fiscal_data');
            }
        });
    }
};
