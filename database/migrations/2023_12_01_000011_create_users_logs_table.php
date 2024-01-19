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
        Schema::create('users_access_logs', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Str::uuid()->toString());
            $table->string('user_id')->nullable()->comment('id de usuario');
            $table->string('browser')->nullable()->comment('Navegador WEB');
            $table->string('ip')->nullable()->comment('ip de solicitud');
            $table->longText('token')->nullable()->comment('token otorgado');
            $table->string('expired_token')->nullable()->comment('expiracion del token otorgado');
            $table->longText('request')->nullable()->comment('solicitud');
            $table->longText('response')->nullable()->comment('repuesta');
            //Auditoría 
            $table->timestamps();
 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_access_logs');
    }
};
