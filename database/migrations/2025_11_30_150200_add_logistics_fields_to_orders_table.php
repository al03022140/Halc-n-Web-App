<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'route_user_id')) {
                $table->foreignId('route_user_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'has_incident')) {
                $table->boolean('has_incident')->default(false)->after('notes');
            }

            if (! Schema::hasColumn('orders', 'incident_notes')) {
                $table->text('incident_notes')->nullable()->after('has_incident');
            }

            if (! Schema::hasColumn('orders', 'missing_items')) {
                $table->text('missing_items')->nullable()->after('incident_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'route_user_id')) {
                $table->dropConstrainedForeignId('route_user_id');
            }

            if (Schema::hasColumn('orders', 'missing_items')) {
                $table->dropColumn('missing_items');
            }

            if (Schema::hasColumn('orders', 'incident_notes')) {
                $table->dropColumn('incident_notes');
            }

            if (Schema::hasColumn('orders', 'has_incident')) {
                $table->dropColumn('has_incident');
            }
        });
    }
};
