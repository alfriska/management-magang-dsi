<?php

namespace App\Http\Controllers;

use App\Models\Intern;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\Report;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isIntern()) {
            return $this->internDashboard();
        }

        return $this->adminDashboard();
    }

    private function adminDashboard()
    {
        $totalInterns = Intern::where('status', 'active')->count();
        $pendingRegistrations = Intern::where('status', 'pending')->count();
        
        // Batch all task statistics queries together
        $taskStats = Task::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status IN ("pending", "in_progress") THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = "completed" AND is_late = 0 THEN 1 ELSE 0 END) as completed_on_time,
            SUM(CASE WHEN status = "completed" AND is_late = 1 THEN 1 ELSE 0 END) as completed_late
        ')->first();
        
        $totalTasks = $taskStats->total;
        $completedTasks = $taskStats->completed;
        $pendingTasks = $taskStats->pending;
        $completedOnTime = $taskStats->completed_on_time;
        $completedLate = $taskStats->completed_late;

        // Batch today's attendance statistics
        $todayAttendanceStats = Attendance::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status IN ("present", "late") THEN 1 ELSE 0 END) as present
        ')->whereDate('date', today())->first();
        
        $todayAttendance = $todayAttendanceStats->total ?? 0;
        $presentToday = $todayAttendanceStats->present ?? 0;

        // Eager load recent records with relationships
        $recentTasks = Task::with(['intern.user'])
            ->latest()
            ->take(5)
            ->get();

        $recentAttendances = Attendance::with(['intern.user'])
            ->whereDate('date', today())
            ->latest()
            ->take(10)
            ->get();

        // Tasks waiting for review
        $submittedTasks = Task::with(['intern.user'])
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'asc')
            ->get();

        // Only load essential relationships, avoid loading all tasks/attendances/assessments
        $interns = Intern::with(['user'])
            ->where('status', 'active')
            ->limit(100) // Add reasonable limit
            ->get();

        // Chart Data: Today's Attendance Breakdown (single query with aggregation)
        $attendanceBreakdown = Attendance::selectRaw('
            status,
            COUNT(*) as count
        ')->whereDate('date', today())
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $attendanceToday = [
            'present' => $attendanceBreakdown['present'] ?? 0,
            'late' => $attendanceBreakdown['late'] ?? 0,
            'permission' => $attendanceBreakdown['permission'] ?? 0,
            'sick' => $attendanceBreakdown['sick'] ?? 0,
            'absent' => $totalInterns - $todayAttendance,
        ];

        // Chart Data: Monthly Attendance Trend (Last 7 days) - Single optimized query
        $attendanceTrend = [];
        $trendData = Attendance::selectRaw('
            DATE_FORMAT(date, "%Y-%m-%d") as date_key,
            SUM(CASE WHEN status IN ("present", "late") THEN 1 ELSE 0 END) as present
        ')->whereBetween('date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy('date_key')
            ->orderBy('date_key', 'asc')
            ->get()
            ->keyBy('date_key');

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $present = isset($trendData[$dateKey]) ? (int) $trendData[$dateKey]->present : 0;
            
            $attendanceTrend[] = [
                'date' => $date->format('d M'),
                'present' => $present,
                'absent' => $totalInterns - $present,
            ];
        }

        return view('dashboard.admin', compact(
            'totalInterns',
            'pendingRegistrations',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'completedOnTime',
            'completedLate',
            'todayAttendance',
            'presentToday',
            'recentTasks',
            'recentAttendances',
            'submittedTasks',
            'interns',
            'attendanceToday',
            'attendanceTrend'
        ));
    }

    private function internDashboard()
    {
        $user = auth()->user();
        $intern = $user->intern;

        if (!$intern) {
            return view('dashboard.incomplete-profile');
        }

        $tasks = $intern->tasks()->latest()->take(5)->get();
        $attendances = $intern->attendances()->latest()->take(7)->get();
        $todayAttendance = $intern->attendances()->whereDate('date', today())->first();

        // Batch task statistics in a single query instead of multiple queries
        $taskCounts = $intern->tasks()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status IN ("pending", "in_progress") THEN 1 ELSE 0 END) as pending
            ')->first();

        $completedTasks = $taskCounts->completed ?? 0;
        $pendingTasks = $taskCounts->pending ?? 0;
        $totalTasks = $taskCounts->total ?? 0;

        // Task submission statistics
        $taskStats = $intern->getTaskStatistics();
        $onTimeRate = $intern->getOnTimeRate();

        $attendancePercentage = $intern->getAttendancePercentage();
        $averageSpeed = $intern->getAverageSpeed();
        $overallScore = $intern->getOverallScore();

        // Office Location Settings
        $officeLat = \App\Models\Setting::get('office_latitude', -7.052683);
        $officeLon = \App\Models\Setting::get('office_longitude', 110.469375);
        $maxDist = \App\Models\Setting::get('max_checkin_distance', 100);

        // Chart Data: Weekly Attendance (Last 7 days) - Single optimized query
        $weeklyAttendanceData = $intern->attendances()
            ->selectRaw('DATE(date) as date, status')
            ->whereBetween('date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy(function ($item) {
                return $item['date'];
            });

        $weeklyAttendance = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $att = $weeklyAttendanceData[$dateKey] ?? null;
            
            $weeklyAttendance[] = [
                'date' => $date->format('D'),
                'status' => $att ? $att->status : 'absent',
            ];
        }

        // Chart Data: Task Status Breakdown - Single query instead of 5 separate queries
        $taskBreakdownData = $intern->tasks()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $taskBreakdown = [
            'pending' => $taskBreakdownData['pending'] ?? 0,
            'in_progress' => $taskBreakdownData['in_progress'] ?? 0,
            'submitted' => $taskBreakdownData['submitted'] ?? 0,
            'completed' => $taskBreakdownData['completed'] ?? 0,
            'revision' => $taskBreakdownData['revision'] ?? 0,
        ];

        return view('dashboard.intern', compact(
            'intern',
            'tasks',
            'attendances',
            'todayAttendance',
            'completedTasks',
            'pendingTasks',
            'totalTasks',
            'taskStats',
            'onTimeRate',
            'attendancePercentage',
            'averageSpeed',
            'overallScore',
            'officeLat',
            'officeLon',
            'maxDist',
            'weeklyAttendance',
            'taskBreakdown'
        ));
    }
}
