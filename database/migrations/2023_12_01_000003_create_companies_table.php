<?php

use App\Enums\SMTPSecurityType;
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
        Schema::create('companies', function (Blueprint $table) {
            $table->comment('Entidad de empresa');
            $table->id();
            $table->string('RUC', 20)->unique()->comment('RUC');
            $table->string('company_name', 100)->comment('razón social');
            $table->string('commercial_name', 100)->comment('Nombre comercial');
            $table->string('patch_logo')->comment('directorio del logo');
            $table->string('patch_folder')->comment('directorio de empresa');

            //correo
            $table->boolean('can_send_email')->default(0)->comment('establece si se puede enviar correo');
            $table->boolean('can_used_smtp')->default(0)->comment('establece si usa una configuracion SMTP propia');
            $table->string('smtp_server')->nullable()->comment('servidor correo');
            $table->string('smtp_port')->nullable()->comment('puerto correo');
            $table->string('smtp_email')->comment('correo');
            $table->enum('smtp_type_security', SMTPSecurityType::getValues())->default(SMTPSecurityType::SSL)->comment('tipo de seguridad');
            $table->string('smtp_user')->nullable()->comment('usuario correo');
            $table->string('smtp_password')->nullable()->comment('password correo');

            //correo
            $table->string('api_key')->comment('API Key para receibir documentos');

            //Auditoría
            $table->unsignedBigInteger('created_user');
            $table->unsignedBigInteger('updated_user')->nullable();
            $table->timestamps();

            $table->boolean('state')->default(true)->comment('Estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
