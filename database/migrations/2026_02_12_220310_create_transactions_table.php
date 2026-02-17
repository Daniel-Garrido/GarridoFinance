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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->date('date'); // fecha del movimiento
        $table->enum('type', ['income', 'expense']); // ingreso o gasto
        $table->decimal('amount', 12, 2); // monto

        $table->foreignId('account_id')
              ->constrained('accounts')
              ->cascadeOnDelete();

        $table->foreignId('category_id')
              ->constrained('categories')
              ->cascadeOnDelete();

        $table->foreignId('payment_method_id')
              ->constrained('payment_methods')
              ->cascadeOnDelete();

        $table->string('description')->nullable(); // nota o descripción
        $table->boolean('is_historical')->default(false); // opcional: año anterior

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
