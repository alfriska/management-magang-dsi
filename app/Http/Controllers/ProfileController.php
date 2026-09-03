<?php

namespace App\Http\Controllers;

use App\Models\Intern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $intern = $user->intern;
        
        $stats = null;
        $taskStatusData = [];
        $attendanceData = [];
        $assessmentData = [];
        
        if ($intern) {
            $intern->load(['supervisor']);
            
            // Load limited task relationships
            $intern->load(['tasks' => function($q) { $q->latest()->limit(100); }]);
            $intern->load(['attendances' => function($q) { $q->latest()->limit(100); }]);
            $intern->load(['assessments' => function($q) { $q->latest()->limit(5); }]);
            
            // Use database queries for counting instead of in-memory operations
            $stats = [
                'totalTasks' => $intern->tasks()->count(),
                'completedTasks' => $intern->tasks()->where('status', 'completed')->count(),
                'pendingTasks' => $intern->tasks()->whereIn('status', ['pending', 'in_progress'])->count(),
                'attendancePercentage' => $intern->getAttendancePercentage(),
                'averageSpeed' => $intern->getAverageSpeed(),
                'overallScore' => $intern->getOverallScore(),
            ];

            // Task status pie chart data - use database queries
            $taskStatusData = [
                'Selesai' => $intern->tasks()->where('status', 'completed')->count(),
                'Dalam Proses' => $intern->tasks()->where('status', 'in_progress')->count(),
                'Menunggu' => $intern->tasks()->where('status', 'pending')->count(),
                'Revisi' => $intern->tasks()->where('status', 'revision')->count(),
            ];

            // Attendance pie chart data - use database queries
            $attendanceData = [
                'Hadir' => $intern->attendances()->where('status', 'present')->count(),
                'Terlambat' => $intern->attendances()->where('status', 'late')->count(),
                'Tidak Hadir' => $intern->attendances()->where('status', 'absent')->count(),
                'Sakit' => $intern->attendances()->where('status', 'sick')->count(),
                'Izin' => $intern->attendances()->where('status', 'permission')->count(),
            ];

            // Assessment radar chart data - using limited loaded assessments
            $latestAssessments = $intern->assessments;
            if ($latestAssessments->isNotEmpty()) {
                $assessmentData = [
                    'Kualitas' => round($latestAssessments->avg('quality_score'), 1),
                    'Kecepatan' => round($latestAssessments->avg('speed_score'), 1),
                    'Inisiatif' => round($latestAssessments->avg('initiative_score'), 1),
                    'Kerjasama' => round($latestAssessments->avg('teamwork_score'), 1),
                    'Komunikasi' => round($latestAssessments->avg('communication_score'), 1),
                ];
            }
        }
        
        return view('profile.show', compact('user', 'intern', 'stats', 'taskStatusData', 'attendanceData', 'assessmentData'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }
            
            $avatarName = time() . '.' . $request->avatar->extension();
            $request->avatar->storeAs('avatars', $avatarName, 'public');
            $validated['avatar'] = $avatarName;
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }
}
