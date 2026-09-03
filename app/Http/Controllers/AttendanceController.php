<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Intern;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Attendance::with(['intern.user']);

        // If intern, only show their own attendance
        if ($user->isIntern() && $user->intern) {
            $query->where('intern_id', $user->intern->id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('intern_id')) {
            $query->where('intern_id', $request->intern_id);
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('date', $date->month)
                  ->whereYear('date', $date->year);
        }

        $attendances = $query->latest('date')->paginate(15);
        $interns = Intern::with('user')->where('status', 'active')->get();

        return view('attendances.index', compact('attendances', 'interns'));
    }

    public function create()
    {
        $interns = Intern::with('user')->where('status', 'active')->get();
        return view('attendances.create', compact('interns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'intern_id' => 'required|exists:interns,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,late,absent,sick,permission',
            'notes' => 'nullable|string',
        ]);

        // Check if attendance already exists for this date
        $existing = Attendance::where('intern_id', $validated['intern_id'])
            ->whereDate('date', $validated['date'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Presensi untuk tanggal ini sudah ada!');
        }

        Attendance::create($validated);

        return redirect()->route('attendances.index')
            ->with('success', 'Presensi berhasil ditambahkan!');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['intern.user']);

        // Get office location settings for map
        $officeLat = \App\Models\Setting::get('office_latitude', -7.052683);
        $officeLon = \App\Models\Setting::get('office_longitude', 110.469375);
        $maxDistance = \App\Models\Setting::get('max_checkin_distance', 100);

        return view('attendances.show', compact('attendance', 'officeLat', 'officeLon', 'maxDistance'));
    }

    public function edit(Attendance $attendance)
    {
        $interns = Intern::with('user')->where('status', 'active')->get();
        return view('attendances.edit', compact('attendance', 'interns'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'intern_id' => 'nullable|exists:interns,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,late,absent,sick,permission',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Handle empty intern_id
        if (empty($validated['intern_id'])) {
            $validated['intern_id'] = null;
        }

        $attendance->update($validated);

        return redirect()->route('attendances.index')
            ->with('success', 'Presensi berhasil diperbarui!');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendances.index')
            ->with('success', 'Presensi berhasil dihapus!');
    }

    // Helper function untuk hitung jarak (Haversine Formula) di dalam controller yang sama
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat/2) * sin($dLat/2) + cos($lat1) * cos($lat2) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    // Check in for intern
    public function checkIn(Request $request)
    {
        $user = auth()->user();

        if (!$user->isIntern() || !$user->intern) {
            return back()->with('error', 'Akses ditolak.');
        }

        $intern = $user->intern;
        $today = Carbon::today();
        $now = Carbon::now();

        // Check if already checked in today
        $attendance = Attendance::where('intern_id', $intern->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            return back()->with('error', 'Anda sudah melakukan check-in hari ini!');
        }

        // --- GPS VALIDATION START ---
        $lat = $request->input('latitude');
        $lon = $request->input('longitude');

        // Jika user memaksa hit API tanpa lat/long
        if (!$lat || !$lon) {
            return back()->with('error', 'Lokasi tidak ditemukan. Pastikan GPS aktif dan browser diizinkan mengakses lokasi.');
        }

        $officeLat = \App\Models\Setting::get('office_latitude', -7.052683);
        $officeLon = \App\Models\Setting::get('office_longitude', 110.469375);
        $maxDistance = \App\Models\Setting::get('max_checkin_distance', 100); // 100 meter default

        $distance = $this->calculateDistance($lat, $lon, $officeLat, $officeLon);
        $distance = round($distance);

        if ($distance > $maxDistance) {
            return back()->with('error', "Anda di luar jangkauan kantor! Jarak: {$distance}m (Max: {$maxDistance}m). Lokasi: PT. DUTA SOLUSI INFORMATIKA.");
        }
        // --- GPS VALIDATION END ---

        // Get Check-in Settings
        $startTime = \App\Models\Setting::get('office_start_time', '08:00');
        $lateLimit = \App\Models\Setting::get('late_tolerance_time', '08:15');

        // Logic check-in time
        $currentTime = $now->format('H:i');

        // Check Status
        $isLate = $currentTime > $lateLimit;

        // If late but no reason provided, return with flag to show modal
        if ($isLate && !$request->filled('late_reason')) {
            return back()
                ->with('show_late_reason_form', true)
                ->with('pending_checkin', [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'distance' => $distance,
                ]);
        }

        if ($isLate) {
            $status = 'late';
            $message = 'Check-in berhasil (Terlambat). Jam masuk: ' . $startTime;
        } else {
            $status = 'present';
            $message = 'Check-in berhasil tepat waktu!';
        }

        Attendance::create([
            'intern_id' => $intern->id,
            'date' => $today,
            'check_in' => $currentTime,
            'status' => $status,
            'late_reason' => $isLate ? $request->input('late_reason') : null,
            'latitude' => $lat,
            'longitude' => $lon,
            'distance_meters' => $distance,
        ]);

        return back()->with('success', $message . " Jarak dari kantor: {$distance}m");
    }

    // Check out for intern
    public function checkOut()
    {
        $user = auth()->user();

        if (!$user->isIntern() || !$user->intern) {
            return back()->with('error', 'Akses ditolak.');
        }

        $intern = $user->intern;
        $today = Carbon::today();

        $attendance = Attendance::where('intern_id', $intern->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan check-in hari ini!');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Anda sudah melakukan check-out hari ini!');
        }

        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        $endTime = \App\Models\Setting::get('office_end_time', '17:00');

        $attendance->update([
            'check_out' => $currentTime,
        ]);

        if ($currentTime < $endTime) {
            $message = 'Presensi Keluar berhasil (Pulang Cepat). Jam pulang seharusnya: ' . $endTime;
            return back()->with('warning', $message); // Use warning toast if available, or just success with text
        }

        return back()->with('success', 'Presensi Keluar berhasil pada ' . $currentTime . '! Hati-hati di jalan.');
    }

    public function submitPermission(Request $request)
    {
        $user = auth()->user();

        if (!$user->isIntern() || !$user->intern) {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'status' => 'required|in:sick,permission',
            'notes' => 'required|string|max:500',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $intern = $user->intern;
        $today = Carbon::today();

        // Check if already exist
        $existing = Attendance::where('intern_id', $intern->id)
            ->whereDate('date', $today)
            ->first();
        
        if ($existing) {
            return back()->with('error', 'Anda sudah mengisi presensi untuk hari ini.');
        }

        $filePath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = time() . '_' . $intern->id . '.' . $file->getClientOriginalExtension();
            
            // Simpan menggunakan disk 'public' (storage/app/public)
            // Hasilnya path = 'attendance_proofs/filename.ext'
            $filePath = $file->storeAs('attendance_proofs', $filename, 'public');
        }

        Attendance::create([
            'intern_id' => $intern->id,
            'date' => $today,
            'check_in' => null,
            'check_out' => null,
            'status' => $request->status,
            'notes' => $request->notes,
            'proof_file' => $filePath,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return back()->with('success', 'Izin berhasil diajukan.');
    }
}
