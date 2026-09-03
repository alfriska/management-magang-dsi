<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Intern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isIntern()) {
            abort(403, 'Halaman ini hanya untuk intern.');
        }

        $intern = $user->intern;
        if (!$intern) {
            return redirect()->route('dashboard')->with('error', 'Profil magang tidak ditemukan.');
        }

        $query = $intern->dailyReports()->orderBy('date', 'desc');

        $filterDate = $request->get('date');
        if ($filterDate) {
            $query->whereDate('date', $filterDate);
        }

        $dailyReports = $query->get();

        $todayReport = $intern->dailyReports()
            ->whereDate('date', now()->toDateString())
            ->first();

        return view('daily-reports.index', [
            'dailyReports' => $dailyReports,
            'filterDate' => $filterDate,
            'todayReport' => $todayReport,
            'siswaName' => $user->name,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isIntern()) {
            abort(403, 'Hanya intern yang dapat membuat Daily Report.');
        }

        $intern = $user->intern;
        if (!$intern) {
            abort(403, 'Profil magang tidak ditemukan.');
        }

        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'description' => 'required|string',
            'target_date' => 'nullable|date',
            'status' => 'required|in:belum_dikerjakan,on_progress,selesai',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $exists = $intern->dailyReports()
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Daily Report untuk tanggal tersebut sudah ada.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $intern->id . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('daily_report_images', $filename, 'public');
        }

        $intern->dailyReports()->create([
            'created_by' => Auth::id(),
            'date' => $request->date,
            'description' => $request->description,
            'target_date' => $request->target_date,
            'status' => $request->status,
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('daily-reports.index')
            ->with('success', 'Daily Report berhasil disimpan.');
    }

    public function update(Request $request, DailyReport $dailyReport)
    {
        $user = Auth::user();

        if (!$user->isIntern()) {
            abort(403, 'Hanya intern yang dapat mengubah Daily Report.');
        }

        $intern = $user->intern;
        if (!$intern || $dailyReport->intern_id !== $intern->id) {
            abort(403, 'Anda tidak memiliki akses ke Daily Report ini.');
        }

        if (!$dailyReport->date->isToday()) {
            abort(403, 'Daily Report hanya bisa diubah pada hari yang sama.');
        }

        $request->validate([
            'description' => 'required|string',
            'target_date' => 'nullable|date',
            'status' => 'required|in:belum_dikerjakan,on_progress,selesai',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'description' => $request->description,
            'target_date' => $request->target_date,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            if ($dailyReport->image) {
                Storage::disk('public')->delete($dailyReport->image);
            }
            $file = $request->file('image');
            $filename = time() . '_' . $intern->id . '.' . $file->getClientOriginalExtension();
            $data['image'] = $file->storeAs('daily_report_images', $filename, 'public');
        }

        $dailyReport->update($data);

        return redirect()
            ->route('daily-reports.index')
            ->with('success', 'Daily Report berhasil diperbarui.');
    }

    public function show(DailyReport $dailyReport)
    {
        $user = Auth::user();
        $dailyReport->loadMissing('intern.user');

        if ($user->isIntern()) {
            $intern = $user->intern;
            if (!$intern || $dailyReport->intern_id !== $intern->id) {
                abort(403, 'Anda tidak memiliki akses ke Daily Report ini.');
            }
        } elseif ($user->role === 'pembimbing') {
            if ($dailyReport->intern->supervisor_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke Daily Report intern ini.');
            }
        } elseif ($user->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return view('daily-reports.show', compact('dailyReport'));
    }

    public function monitoring(Request $request)
    {
        $user = Auth::user();

        if (!$user->canManage()) {
            abort(403, 'Halaman ini hanya untuk admin/pembimbing.');
        }

        $scopeQuery = Intern::query();
        if ($user->role === 'pembimbing') {
            $scopeQuery->where('supervisor_id', $user->id);
        }

        $schools = (clone $scopeQuery)
            ->whereNotNull('school')
            ->distinct()
            ->orderBy('school')
            ->pluck('school');

        $internsQuery = clone $scopeQuery;
        $filterSchool = $request->get('school');
        if ($filterSchool) {
            $internsQuery->where('school', $filterSchool);
        }

        $internIds = $internsQuery->pluck('id');

        $query = DailyReport::with('intern.user')
            ->whereIn('intern_id', $internIds)
            ->orderBy('date', 'desc');

        $filterDate = $request->get('date');
        if ($filterDate) {
            $query->whereDate('date', $filterDate);
        }

        $dailyReports = $query->get();

        $summaryDate = $filterDate ?: now()->toDateString();
        $totalActiveInterns = (clone $internsQuery)->where('status', 'active')->count();
        $sudahLaporCount = DailyReport::whereIn('intern_id', $internIds)
            ->whereDate('date', $summaryDate)
            ->count();
        $belumLaporCount = max($totalActiveInterns - $sudahLaporCount, 0);

        return view('daily-reports.monitoring', [
            'dailyReports' => $dailyReports,
            'filterDate' => $filterDate,
            'filterSchool' => $filterSchool,
            'schools' => $schools,
            'sudahLaporCount' => $sudahLaporCount,
            'belumLaporCount' => $belumLaporCount,
            'summaryDate' => $summaryDate,
        ]);
    }

    public function destroy(DailyReport $dailyReport)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus Daily Report.');
        }

        if ($dailyReport->image) {
            Storage::disk('public')->delete($dailyReport->image);
        }

        $dailyReport->delete();

        return redirect()
            ->route('daily-reports.monitoring')
            ->with('success', 'Daily Report berhasil dihapus.');
    }
}