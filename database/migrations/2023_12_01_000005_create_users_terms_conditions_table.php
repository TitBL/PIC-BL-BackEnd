<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_terms_conditions', function (Blueprint $table) {
            $table->comment('Entidad para el histórico de terminos y condiciones');
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->unsignedBigInteger('user_id')->comment('id de usuario');
            $table->string('details_terms')->nullable()->comment('detalle del término aceptado');
            $table->boolean('terms_accept')->nullable()->comment('estado de terminos de aceptación');
            $table->timestamps();
            //Foreign key
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_terms_conditions');
    }
};
