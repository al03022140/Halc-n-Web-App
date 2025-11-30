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
        Schema::table('orders', function (Blueprint $table) {
            // Añadir relaciones con cliente y producto
            $table->unsignedBigInteger('client_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('product_id')->nullable()->after('client_id');
            
            // Eliminar solo fiscal_data, mantener customer_name y customer_number para tracking
            $table->dropColumn(['fiscal_data']);
            
            // Añadir cantidad de producto
            $table->integer('quantity')->default(1)->after('product_id');
            
            // Añadir llaves foráneas
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Eliminar llaves foráneas
            $table->dropForeign(['client_id']);
            $table->dropForeign(['product_id']);
            
            // Eliminar columnas añadidas
            $table->dropColumn(['client_id', 'product_id', 'quantity']);
            
            // Restaurar solo fiscal_data
            $table->string('fiscal_data')->after('customer_number');
        });
    }
};
