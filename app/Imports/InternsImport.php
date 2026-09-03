<?php

namespace App\Imports;

use App\Models\Intern;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class InternsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $supervisorId;
    protected $imported = 0;

    public function __construct($supervisorId = null)
    {
        $this->supervisorId = $supervisorId;
    }

    public function isEmptyRow(array $row): bool
    {
        return empty($row['nama']) && empty($row['email']);
    }

    public function model(array $row)
    {


        // Skip rows that don't have basic info
        if (empty($row['nama']) || empty($row['email'])) {
            return null;
        }

        // Check if email already exists - skip if duplicate
        if (User::where('email', $row['email'])->exists()) {
            return null; // Skip this row, email already exists
        }

        // Try to find supervisor by name if provided in Excel and not set via form
        $resolvedSupervisorId = $this->supervisorId;
        if (!$resolvedSupervisorId && !empty($row['pembimbing'])) {
            $supervisor = User::where('name', 'like', '%' . $row['pembimbing'] . '%')
                ->whereIn('role', ['admin', 'pembimbing', 'supervisor'])
                ->first();
            if ($supervisor) {
                $resolvedSupervisorId = $supervisor->id;
            }
        }

        // Create User first
        $user = User::create([
            'name' => $row['nama'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'role' => 'intern',
        ]);

        $this->imported++;

        // Map status
        $status = 'active';
        if (isset($row['status'])) {
            $rowStatus = strtolower($row['status']);
            if ($rowStatus === 'aktif' || $rowStatus === 'active') $status = 'active';
            elseif ($rowStatus === 'selesai' || $rowStatus === 'completed') $status = 'completed';
            elseif ($rowStatus === 'tidak aktif' || $rowStatus === 'inactive') $status = 'inactive';
        }

        // Create Intern
        return new Intern([
            'user_id' => $user->id,
            'nis' => $row['nis'] ?? ($row['id'] ?? null),
            'school' => $row['sekolah'] ?? ($row['school'] ?? null),
            'department' => $row['jurusan'] ?? ($row['department'] ?? null),
            'phone' => $row['no_telepon'] ?? ($row['telepon'] ?? ($row['phone'] ?? null)),
            'address' => $row['alamat'] ?? ($row['address'] ?? null),
            'start_date' => $this->parseDate($row['tanggal_mulai'] ?? ($row['start_date'] ?? null)),
            'end_date' => $this->parseDate($row['tanggal_selesai'] ?? ($row['end_date'] ?? null)),
            'supervisor_id' => $resolvedSupervisorId,
            'status' => $status,
        ]);
    }

    public function rules(): array
    {
        return [
            // Core fields - truly required
            '*.nama' => 'nullable|string|max:255',
            '*.email' => 'nullable|email',

            // Other fields - optional
            '*.nis' => 'nullable|string|max:50',
            '*.sekolah' => 'nullable|string|max:255',
            '*.jurusan' => 'nullable|string|max:255',
            '*.no_telepon' => 'nullable|string|max:20',
            '*.alamat' => 'nullable|string',
            '*.tanggal_mulai' => 'nullable',
            '*.tanggal_selesai' => 'nullable',
            '*.status' => 'nullable|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required' => 'Kolom Nama wajib diisi.',
            'email.required' => 'Kolom Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ];
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel date format (numeric)
        if (is_numeric($value)) {
            try {
                return Carbon::createFromTimestamp(($value - 25569) * 86400);
            } catch (\Exception $e) {
                // ignore and try other methods
            }
        }

        // Handle Indonesian format d/m/Y or d-m-Y
        $value = str_replace('-', '/', $value);
        try {
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            // ignore and try standard parse
        }

        // Handle string date formats
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }
}
