<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parameters', function (Blueprint $table) {
            $table->comment('Entidad para la gestión de configuraciones en base de datos');
            $table->string('parameter_name')->primary()->comment('Nombre del parametro');
            $table->string('parameter_value')->comment('valor del parametro');

            //Auditoría 
            $table->timestamps();
        });

        $this->set_default_data();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }


    /**
     * Genera los roles principales
     * 
     * @return void
     */
    function set_default_data(): void
    {
        $date = Carbon::now()->format('Y-m-d\TH:i:s.v');
        foreach (self::$parameters as $parameter) {

            DB::table('parameters')->insert([
                'parameter_name' => $parameter[0],
                'parameter_value' => $parameter[1],
                'created_at' => $date
            ]);
        }
    }

    /*
    * Roles de usuario por default en la base de datos
    */
    static $parameters = [
        ["mail_mailer", "smtp"],
        ["smtp_server", ""],
        ["smtp_port", "465"],
        ["smtp_email", ""],
        ["smtp_type_security", "SSL/TLS"],
        ["smtp_user", ""],
        ["smtp_password", ""],
    ];
};
