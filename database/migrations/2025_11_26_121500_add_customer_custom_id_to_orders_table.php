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
            if (! Schema::hasColumn('orders', 'customer_custom_id')) {
                $table->string('customer_custom_id')->nullable()->after('customer_number');
                $table->index('customer_custom_id');
            }
        });

        if (Schema::hasColumn('orders', 'client_id') && Schema::hasColumn('clients', 'custom_id')) {
            DB::statement('UPDATE orders SET customer_custom_id = (SELECT custom_id FROM clients WHERE clients.id = orders.client_id) WHERE customer_custom_id IS NULL');
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_custom_id')) {
                $table->dropIndex('orders_customer_custom_id_index');
                $table->dropColumn('customer_custom_id');
            }
        });
    }
};
