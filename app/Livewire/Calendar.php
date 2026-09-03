<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Intern;
use App\Models\DailyReport;
use App\Helpers\IndonesianHoliday;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Calendar View')]
class Calendar extends Component
{
    public $currentMonth;
    public $currentYear;
    public $days = [];
    public $events = [];
    public $dailyReportMap = [];
    public $holidays = []; // Indonesian national holidays
    public $viewMode = 'attendance'; // 'attendance' or 'tasks'

    // Modal state
    public $showModal = false;
    public $selectedDate = null;
    public $modalData = [];

    public function mount($mode = 'attendance')
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->viewMode = $mode;
        $this->generateCalendar();
    }

    public function generateCalendar()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $date->daysInMonth;
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Get the day of week for the first day (0 = Sunday, 6 = Saturday)
        $startDayOfWeek = $startOfMonth->dayOfWeek;

        $this->days = [];
        $this->events = [];
        
        // Load Indonesian national holidays
        $this->holidays = IndonesianHoliday::getMonthHolidays($this->currentYear, $this->currentMonth);

        // Add empty slots for days before the start of month
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $this->days[] = null;
        }

        // Add actual days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $this->days[] = $day;
        }

        // Load events based on view mode
        $this->loadEvents($startOfMonth, $endOfMonth);
    }

    public function loadEvents($start, $end)
    {
        $user = auth()->user();

        if ($this->viewMode === 'attendance') {
            $this->loadAttendanceEvents($user, $start, $end);
        } else {
            $this->loadTaskEvents($user, $start, $end);
        }
    }

    public function loadAttendanceEvents($user, $start, $end)
    {
        if ($user->isIntern() && $user->intern) {
            $attendances = Attendance::where('intern_id', $user->intern->id)
                ->whereBetween('date', [$start, $end])
                ->get();
        } elseif ($user->canManage()) {
            // For admin/supervisor, show summary counts
            $attendances = Attendance::whereBetween('date', [$start, $end])
                ->selectRaw('date, status, COUNT(*) as count')
                ->groupBy('date', 'status')
                ->get();
        } else {
            $attendances = collect();
        }

        foreach ($attendances as $att) {
            $day = Carbon::parse($att->date)->day;
            if (!isset($this->events[$day])) {
                $this->events[$day] = [];
            }

            if ($user->isIntern()) {
                $this->events[$day][] = [
                    'type' => 'attendance',
                    'status' => $att->status,
                    'check_in' => $att->check_in,
                    'check_out' => $att->check_out,
                ];
            } else {
                $this->events[$day][] = [
                    'type' => 'attendance_summary',
                    'status' => $att->status,
                    'count' => $att->count,
                ];
            }
        }
    }

    public function loadTaskEvents($user, $start, $end)
    {
        if ($user->canManage()) {
            // For admin: show task assignments grouped by deadline
            $assignments = TaskAssignment::with('tasks')
                ->whereBetween('deadline', [$start, $end])
                ->get();

            foreach ($assignments as $assignment) {
                if (!$assignment->deadline) continue;

                $day = Carbon::parse($assignment->deadline)->day;
                if (!isset($this->events[$day])) {
                    $this->events[$day] = [];
                }

                $completedCount = $assignment->tasks->where('status', 'completed')->count();
                $totalCount = $assignment->tasks->count();

                $this->events[$day][] = [
                    'type' => 'task_assignment',
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'priority' => $assignment->priority,
                    'completed' => $completedCount,
                    'total' => $totalCount,
                ];
            }
        } else {
            // For interns: show individual tasks
            $query = Task::whereBetween('deadline', [$start, $end]);

            if ($user->isIntern() && $user->intern) {
                $query->where('intern_id', $user->intern->id);
            }

            $tasks = $query->get();

            foreach ($tasks as $task) {
                if (!$task->deadline) continue;

                $day = Carbon::parse($task->deadline)->day;
                if (!isset($this->events[$day])) {
                    $this->events[$day] = [];
                }

                                $this->events[$day][] = [
                    'type' => 'task',
                    'id' => $task->id,
                    'title' => $task->title,
                    'status' => $task->status,
                    'priority' => $task->priority,
                ];
            }

            // Hubungkan dengan Daily Report intern pada tanggal yang sama
            if ($user->isIntern() && $user->intern) {
                $dailyReports = DailyReport::where('intern_id', $user->intern->id)
                    ->whereBetween('date', [$start, $end])
                    ->get();

                foreach ($dailyReports as $report) {
                    $day = Carbon::parse($report->date)->day;
                    $this->dailyReportMap[$day] = $report->id;
                }
            }
        }
    }

    // Open modal with attendance stats for a specific date
    public function openAttendanceModal($day)
    {
        $user = auth()->user();
        $this->selectedDate = Carbon::createFromDate($this->currentYear, $this->currentMonth, $day);
        $this->modalData = [];

        if ($user->canManage()) {
            // Get detailed attendance for that day
            $attendances = Attendance::with('intern.user')
                ->whereDate('date', $this->selectedDate)
                ->get();

            $totalInterns = Intern::where('status', 'active')->count();
            $present = $attendances->where('status', 'present')->count();
            $late = $attendances->where('status', 'late')->count();
            $permission = $attendances->where('status', 'permission')->count();
            $sick = $attendances->where('status', 'sick')->count();
            $absent = $totalInterns - $attendances->count();

            $this->modalData = [
                'date' => $this->selectedDate->format('d F Y'),
                'total' => $totalInterns,
                'present' => $present,
                'late' => $late,
                'permission' => $permission,
                'sick' => $sick,
                'absent' => max(0, $absent),
                'attendances' => $attendances->map(function($a) {
                    return [
                        'name' => $a->intern->user->name ?? 'N/A',
                        'status' => $a->status,
                        'check_in' => $a->check_in,
                        'check_out' => $a->check_out,
                    ];
                })->toArray(),
            ];

            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalData = [];
    }

    public function previousMonth()
    {
        if ($this->currentMonth == 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
        $this->generateCalendar();
    }

    public function nextMonth()
    {
        if ($this->currentMonth == 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
        $this->generateCalendar();
    }

    public function switchMode($mode)
    {
        $this->viewMode = $mode;
        $this->generateCalendar();
    }

    public function getMonthNameProperty()
    {
        return Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->translatedFormat('F Y');
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}

