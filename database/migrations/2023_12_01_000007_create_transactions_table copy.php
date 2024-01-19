<?php

use App\Enums\DocumentType;
use App\Enums\EmissionType;
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
            $table->comment('Entidad de transacciones');
            $table->string('key_access_sri', 49)->primary()->comment('Clave de Acceso');
            $table->string('emission_type', 20)->comment('Tipo Emision');
            $table->date('emission_date', $precision = 0)->comment('fecha de emisión');
            $table->string('document_type', 50)->comment('tipo de documentos');
            $table->string('document_number', 20)->comment('número de documentos');
            $table->unsignedBigInteger('document_issuer_id')->nullable()->comment('id del emisor del documento');
            $table->string('document_issuer_name', 200)->nullable()->comment('nombre  del emisor del documento');
            $table->unsignedBigInteger('document_receiver_id')->nullable()->comment('id del receptor del documento');
            $table->string('document_receiver_name', 100)->nullable()->comment('nombre del receptor del documento');
            $table->dateTime('date_time_authorization', $precision = 0)->nullable()->comment('fecha de autorización');
            $table->dateTime('date_time_last_query', $precision = 0)->comment('fecha última consulta SRI');
            $table->decimal('amount', $precision = 8, $scale = 2);
            $table->string('state')->nullable()->default('NO AUTORIZADO')->comment('estado del documento en SRI');
            //Auditoría 
            $table->timestamps();


            //Foreign key
            $table->foreign('document_issuer_id')->references('id')->on('companies');
            $table->foreign('document_receiver_id')->references('id')->on('users');
        });


        // Lógica para crear la vista
        // DB::statement('CREATE VIEW example_view AS SELECT * FROM example_table');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');

        // Lógica para eliminar la vista
        // DB::statement('DROP VIEW IF EXISTS example_view');
    }
};
