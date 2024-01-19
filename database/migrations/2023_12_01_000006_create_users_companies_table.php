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
        Schema::create('users_companies', function (Blueprint $table) {
            $table->comment('Entidad para romper la relacion de muchos a muchos');
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('id de usuario');
            $table->unsignedBigInteger('company_id')->comment('id de empresa');

            //Auditoría
            $table->unsignedBigInteger('created_user');
            $table->unsignedBigInteger('updated_user')->nullable();
            $table->timestamps();

            //Foreign key
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_companies');
    }
};
