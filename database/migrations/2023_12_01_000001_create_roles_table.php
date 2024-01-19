<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->comment('Entidad de roles');
            $table->id();
            $table->string('name', 50)->unique()->comment('Nombre del Rol');
            $table->string('description')->nullable()->default('')->comment('Descripción del Rol');

            //Auditoría
            $table->unsignedBigInteger('created_user');
            $table->unsignedBigInteger('updated_user')->nullable();
            $table->timestamps();

            //estado
            $table->boolean('state')->default(true)->comment('Estado');
        });

        $this->set_default_data();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
 
    /**
     * Genera los roles principales
     * 
     * @return void
     */
    function set_default_data(): void
    {
        DB::unprepared('SET IDENTITY_INSERT roles ON');
        foreach (self::$rolesU as $rolU) {

            DB::table('roles')->insert([
                'id' => $rolU[0],
                'name' => $rolU[1],
                'description' => $rolU[2],
                'created_user' => 0,
            ]);
        }
        DB::unprepared('SET IDENTITY_INSERT roles OFF');
    }

    /*
    * Roles de usuario por default en la base de datos
    */
    static $rolesU = [
        [0, 'Consumidor', 'Rol con funcionalidad necesaria para revisar y gestionar sus documentos electrónicos.'],
        [1, 'Master', 'Rol con los más altos privilegios en el sistema, tiene la capacidad de gestionar parámetros generales de la aplicación.']
    ];
};
