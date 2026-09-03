<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    /**
     * Handle the Task "deleted" event.
     * - Detach intern dari pivot table task_assignment_interns
     * - Jika semua task dalam group dihapus, group juga dihapus
     */
    public function deleted(Task $task): void
    {
        Log::info("TaskObserver::deleted triggered for Task ID: {$task->id}, TaskAssignment ID: {$task->task_assignment_id}");
        
        if ($task->task_assignment_id && $task->intern_id) {
            $taskAssignment = TaskAssignment::find($task->task_assignment_id);
            
            if ($taskAssignment) {
                // Detach intern dari pivot table jika tidak ada task lain untuk intern ini
                $otherTasksForIntern = Task::where('task_assignment_id', $task->task_assignment_id)
                    ->where('intern_id', $task->intern_id)
                    ->where('id', '!=', $task->id)
                    ->count();
                
                if ($otherTasksForIntern === 0) {
                    // Tidak ada task lain untuk intern ini di assignment ini, detach dari pivot
                    $taskAssignment->interns()->detach($task->intern_id);
                    Log::info("Detached intern {$task->intern_id} from TaskAssignment {$task->task_assignment_id}");
                }
                
                // Jika group tidak memiliki task lagi, hapus groupnya
                if ($taskAssignment->tasks()->count() === 0) {
                    $taskAssignment->delete();
                    Log::info("Deleted empty TaskAssignment {$task->task_assignment_id}");
                }
            }
        }
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        Log::info("TaskObserver::forceDeleted triggered for Task ID: {$task->id}");
        
        if ($task->task_assignment_id && $task->intern_id) {
            $taskAssignment = TaskAssignment::find($task->task_assignment_id);
            
            if ($taskAssignment) {
                // Detach intern dari pivot table
                $otherTasksForIntern = Task::where('task_assignment_id', $task->task_assignment_id)
                    ->where('intern_id', $task->intern_id)
                    ->where('id', '!=', $task->id)
                    ->count();
                
                if ($otherTasksForIntern === 0) {
                    $taskAssignment->interns()->detach($task->intern_id);
                }
                
                if ($taskAssignment->tasks()->count() === 0) {
                    $taskAssignment->forceDelete();
                }
            }
        }
    }
}
