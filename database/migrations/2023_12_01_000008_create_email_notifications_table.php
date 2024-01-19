<?php

use App\Enums\SendType;
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
        Schema::create('email_notifications', function (Blueprint $table) {
            $table->comment('Entidad para el control de notificaciones por email');
            $table->id();
            $table->enum('send_type', SendType::getValues())->comment('Tipo de envío de documento');
            $table->string('reference')->comment('referencia de la transaccion, id ');
            $table->string('send_to')->comment('dirección de envío');
            $table->string('sent_cc')->nullable()->comment('direccion para enviar con copia');
            $table->dateTime('date_time_send', $precision = 0)->nullable()->comment('fecha de envío');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->string('state')->comment('estado del envío');

            //Auditoría 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notifications');
    }
};
