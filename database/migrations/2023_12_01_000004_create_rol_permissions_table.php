<?php

use App\Enums\Permissions;
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
        Schema::create('rol_permissions', function (Blueprint $table) {
            $table->comment('Entidad para romper la relacion de muchos a muchos');
            $table->id();
            $table->unsignedBigInteger('rol_id')->comment('id de rol');
            $table->enum('permission_id', Permissions::getValues())->comment('nivel de acceso para asignación, R=> Restringido P=>Público');

            //Auditoría
            $table->unsignedBigInteger('created_user');
            $table->unsignedBigInteger('updated_user')->nullable();
            $table->timestamps();

            //Foreign key
            $table->foreign('rol_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol_permissions');
    }
};
