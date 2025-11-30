<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            if (!Schema::hasColumn('order_statuses', 'color')) {
                $table->string('color')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            if (Schema::hasColumn('order_statuses', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};