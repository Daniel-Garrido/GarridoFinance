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

    Schema::create('accounts', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')// Llave foránea que referencia al usuario propietario de la cuenta
              ->constrained()
              ->cascadeOnDelete();

        $table->string('name'); // Nombre de la cuenta
        $table->string('type')->default('cash'); // Tipo de cuenta (efectivo, tarjeta, etc.)
        $table->boolean('is_active')->default(true);// Estado de la cuenta (activa/inactiva)

        $table->timestamps();// created_at y updated_at
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
