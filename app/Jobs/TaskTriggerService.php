<?php

namespace App\Jobs;

use App\Models\Entity\ScheduledTask;
use App\Http\Controllers\EmailNotificactionController;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use PhpParser\Node\Stmt\Else_;

class TaskTriggerService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->executeTask();
    }

    /**
     * Execute the next scheduled task.
     * 
     */
    public function executeTask():void
    {
        try {
            // Get the next scheduled task that is not currently running
            $task = ScheduledTask::where('is_running', false)->orderBy('created_at')->first();
            $function=null;
            $controller = null;
            if ($task) {
                // Set the task as running
                $task->update(['is_running' => true]);

                // Resolve the controller and function dynamically
                $controller = App::make($task->controller);
                $function = $task->function;

                // Call the specified function on the controller
                $controller->$function();

                // Set the task as not running
                $task->update(['is_running' => false]);

                // Log success
                $task->saveLog(SCHEDULED_TASK_SUCCESS, $controller, $function);
            } else {

                // Log when no scheduled tasks are found
                $task->saveLog(SCHEDULED_TASK_NO_SUCCESS, $task->controller, $task->function);
            }
        } catch (\Exception $e) {
            // Handle exceptions and log errors
            $task->update(['is_running' => false]);
            $task->saveLog(SCHEDULED_TASK_ERROR, $controller, $function);
        }
    }

    /**
     * Save a log entry for the scheduled task.
     *
     * @param string $message
     * @param string $controllerCall
     * @param string $functionCall
     * @param string|null $error
     * @return void
     */
    private function saveLog($Message, $controller_call, $function_call, $error = null):void
    {
        Log::channel('daily_scheduled_task_log')->info('Scheduled Task:', [
            'controller_call' => $controller_call,
            'function_call' => $function_call,
            'Message' => isset($Message) ? $Message : null,
            'error' => isset($error) ? $error : null,
        ]);
    }
}
