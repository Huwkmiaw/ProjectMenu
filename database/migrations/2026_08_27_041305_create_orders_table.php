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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 25)->unique();
            $table->enum('order_type', ['dine_in', 'take_away']);
            $table->string('customer_name', 100);
            $table->string('table_number', 10)->nullable();
            $table->text('customer_note')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'paid', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['cash', 'cashless'])->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->decimal('change_amount', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id')->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
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
