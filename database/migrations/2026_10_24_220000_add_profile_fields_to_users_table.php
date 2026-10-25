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
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->nullable()->after('name'); // apellidos
            $table->string('phone', 30)->nullable()->after('email'); // teléfono de contacto
            $table->string('profile_photo_path', 2048)->nullable()->after('phone'); // ruta en el disco "public"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'phone', 'profile_photo_path']);
        });
    }
};
