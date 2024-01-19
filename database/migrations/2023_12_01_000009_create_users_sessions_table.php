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
        Schema::create('users_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Str::uuid()->toString());
            $table->unsignedBigInteger('user_id')->comment('id de usuario');
            $table->longText('token')->nullable()->comment('token otorgado');
            $table->timestamp('expired')->nullable()->comment('expiracion de la session'); //estado
            $table->boolean('state')->default(true)->comment('Estado');
            //Auditoría 
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
        Schema::dropIfExists('users_sessions');
    }
};
