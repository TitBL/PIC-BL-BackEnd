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
        Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('controller', 100);
            $table->string('function', 100);
            $table->boolean('running')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_tasks');
    }

    /**
     * Genera las tareas principales
     * 
     * @return void
     */
    function set_default_data(): void
    {
        $date = Carbon::now()->format('Y-m-d\TH:i:s.v');
        foreach (self::$tasks as $task) {

            DB::table('scheduled_tasks')->insert([
                'controller' => $task[0],
                'function' => $task[1],
                'is_running' => 0,
                'created_at' => $date
            ]);
        }
    }

    static $tasks = [
        ['EmailNotificactionController', 'reviewPendingToSubmit'],
        // ['EmailNotificactionController', ''],
    ];
};
