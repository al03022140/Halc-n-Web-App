<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // quién creó la orden
        $table->unsignedBigInteger('status_id'); // estado actual
        $table->string('invoice_number')->unique();
        $table->string('customer_name');
        $table->string('customer_number');
        $table->string('fiscal_data');
        $table->dateTime('order_date');
        $table->string('delivery_address');
        $table->text('notes')->nullable();
        $table->boolean('is_deleted')->default(false);
        $table->timestamps();
        $table->softDeletes(); // para borrado lógico

        // Llaves foráneas
        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('status_id')->references('id')->on('order_statuses');
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
