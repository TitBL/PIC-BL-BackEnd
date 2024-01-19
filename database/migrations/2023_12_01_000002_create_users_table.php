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
        Schema::create('users', function (Blueprint $table) {
            $table->comment('Entidad de Usuarios');
            $table->id();
            $table->unsignedBigInteger('rol_id')->comment('Id de Rol');
            $table->string('DNI', 20)->unique()->comment('CÉDULA - DNI - RUC');
            $table->string('name', 50)->unique()->comment('NOMBRE DE USUARIO');
            $table->string('full_name', 200)->nullable()->comment('Nombres y apellidos completos');
            $table->string('address')->comment('Dirección');
            $table->string('email')->unique(); 
            $table->string('password');

            //Auditoría
            $table->unsignedBigInteger('created_user')->nullable();
            $table->unsignedBigInteger('updated_user')->nullable();
            $table->timestamps();

            //estado
            $table->boolean('state')->default(true)->comment('Estado');

            //Foreign key
            $table->foreign('rol_id')->references('id')->on('roles');
        });

        $this->set_default_data();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }

    /**
     * Genera los roles principales
     * 
     * @return void
     */
    function set_default_data(): void
    {
        $date = Carbon::now()->format('Y-m-d\TH:i:s.v');

        DB::unprepared('SET IDENTITY_INSERT users ON');
        foreach (self::$usuarios as $usuario) {

            DB::table('users')->insert([
                'id' => $usuario[0],
                'rol_id' => $usuario[1],
                'DNI' => $usuario[2],
                'name' => $usuario[3],
                'full_name' => $usuario[4],
                'address' => $usuario[5],
                'email' => $usuario[6],
                'password' => hashPWD($usuario[7]),
                // 'created_at' => $date,
                'state' => true
            ]);
        }
        DB::unprepared('SET IDENTITY_INSERT users OFF');
    }

    /*
    * Usuario por default en la base de datos
    */
    static $usuarios = [
        [0, 1, '2222222222', 'AzulMaster', 'Usuario Master', 'Ecuador', 'nn@az.com', 'AzSer2023'],
    ];
};
