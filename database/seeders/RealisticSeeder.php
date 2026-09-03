<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Intern;
use App\Models\Supervisor;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RealisticSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | Demo Data Constants
    |--------------------------------------------------------------------------
    */

    private array $firstNames = [
        'Ahmad',
        'Budi',
        'Citra',
        'Dewi',
        'Eka',
        'Fadli',
        'Gita',
        'Hendra',
        'Indah',
        'Joko',
        'Kartika',
        'Lukman',
        'Maya',
        'Naufal',
        'Olivia',
        'Prasetyo',
        'Qori',
        'Rizki',
        'Sari',
        'Taufik',
        'Umi',
        'Vino',
        'Wulan',
        'Yoga',
        'Zahra',
        'Aditya',
        'Bella',
        'Cahyo',
        'Diana',
        'Erwin',
        'Fitri',
        'Galih',
        'Hani',
        'Irfan',
        'Julia',
    ];

    private array $lastNames = [
        'Pratama',
        'Wijaya',
        'Kusuma',
        'Sari',
        'Nugroho',
        'Permana',
        'Santoso',
        'Putra',
        'Wati',
        'Hidayat',
        'Ramadhan',
        'Lestari',
        'Setiawan',
        'Utami',
        'Saputra',
        'Dewi',
        'Kurniawan',
        'Putri',
        'Firmansyah',
        'Handayani',
        'Ramadhani',
        'Anggraini',
        'Prasetya',
        'Maharani',
        'Arifin',
        'Susanti',
        'Wahyudi',
        'Puspita',
        'Haryanto',
        'Safitri',
    ];

    private array $schools = [
        'SMK Negeri 1 Jakarta',
        'SMK Negeri 2 Bandung',
        'SMK Telkom Malang',
        'SMK Informatika Surabaya',
        'Politeknik Negeri Jakarta',
        'Universitas Indonesia',
        'Institut Teknologi Bandung',
        'Universitas Gadjah Mada',
        'Universitas Brawijaya',
        'Politeknik Elektronika Negeri Surabaya',
        'SMK Negeri 4 Malang',
        'SMK Prakarya Internasional',
        'SMK Wikrama Bogor',
        'Universitas Bina Nusantara',
        'Universitas Telkom',
    ];

    private array $departments = [
        'Rekayasa Perangkat Lunak',
        'Teknik Komputer dan Jaringan',
        'Multimedia',
        'Sistem Informasi',
        'Teknik Informatika',
        'Manajemen Informatika',
        'Desain Grafis',
        'Animasi',
        'Broadcasting',
        'Bisnis Digital',
    ];

    private array $taskTitles = [
        'Membuat Landing Page Website',
        'Develop REST API Authentication',
        'Redesign UI Dashboard Admin',
        'Setup CI/CD Pipeline',
        'Database Migration & Optimization',
        'Implementasi Payment Gateway',
        'Unit Testing Module User',
        'Dokumentasi API Swagger',
        'Mobile App - Login Screen',
        'Integrasi Social Media Login',
        'Develop Chat Feature Real-time',
        'Setup Monitoring & Logging',
        'Optimasi Performance Website',
        'Membuat Report Generator PDF',
        'Implementasi Notifikasi Push',
        'Develop E-commerce Cart System',
        'Setup Email Template',
        'Membuat Data Visualization Dashboard',
        'Develop File Upload System',
        'Implementasi Role-based Access Control',
    ];

    private array $taskDescriptions = [
        'Membuat landing page responsive dengan design modern menggunakan Tailwind CSS dan animasi smooth scroll.',
        'Mengembangkan REST API untuk autentikasi menggunakan JWT dengan fitur login, register, dan refresh token.',
        'Melakukan redesign pada halaman dashboard admin untuk meningkatkan user experience dan accessibility.',
        'Melakukan setup continuous integration dan continuous deployment menggunakan GitHub Actions.',
        'Melakukan migrasi database dan optimasi query untuk meningkatkan performa aplikasi.',
        'Mengintegrasikan payment gateway Midtrans untuk proses pembayaran online.',
        'Menulis unit test untuk module user dengan coverage minimal 80%.',
        'Membuat dokumentasi API lengkap menggunakan Swagger/OpenAPI specification.',
        'Develop tampilan login screen untuk aplikasi mobile dengan Flutter.',
        'Integrasi login menggunakan Google dan Facebook OAuth.',
        'Mengembangkan fitur chat real-time menggunakan WebSocket.',
        'Setup monitoring aplikasi menggunakan Prometheus dan Grafana.',
        'Melakukan optimasi performa website termasuk lazy loading dan caching.',
        'Membuat sistem generate report dalam format PDF yang bisa di-download.',
        'Implementasi push notification untuk web dan mobile application.',
        'Develop sistem keranjang belanja lengkap dengan kalkulasi harga dan diskon.',
        'Membuat email template responsive untuk berbagai keperluan notifikasi.',
        'Membuat dashboard visualisasi data menggunakan Chart.js atau D3.js.',
        'Develop sistem upload file dengan validasi tipe dan ukuran file.',
        'Implementasi sistem role dan permission untuk kontrol akses user.',
    ];

    /*
    |--------------------------------------------------------------------------
    | Main Seeder
    |--------------------------------------------------------------------------
    */

    public function run(): void
    {
        $this->command->info('👥 Seeding demo data...');

        $admin = $this->createAdmin();
        $supervisors = $this->createSupervisors();
        $interns = $this->createInterns($supervisors);

        $this->createTaskAssignments($admin, $interns);
        $this->createAttendances($interns);
        $this->createAssessments($admin);
        $this->createReports($interns);

        $this->printSummary();
    }

    /*
    |--------------------------------------------------------------------------
    | User Creation Methods
    |--------------------------------------------------------------------------
    */

    private function createAdmin(): User
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@internhub.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $this->command->info('   ✓ Admin ' . ($admin->wasRecentlyCreated ? 'created' : 'already exists'));

        return $admin;
    }

    private function createSupervisors(): array
    {
        $supervisorData = [
            ['Ir. Bambang Suryadi, M.Kom', 'bambang.suryadi@internhub.id', '198501234567891001'],
            ['Dr. Siti Nurhaliza, S.T., M.T.', 'siti.nurhaliza@internhub.id', '198601234567891002'],
            ['Drs. Agus Hermawan, M.Sc', 'agus.hermawan@internhub.id', '198701234567891003'],
        ];

        $supervisors = [];
        foreach ($supervisorData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data[1]],
                [
                    'name' => $data[0],
                    'password' => Hash::make('password'),
                    'role' => 'pembimbing',
                ]
            );

            // Create supervisor profile if model exists
            if (class_exists(Supervisor::class)) {
                Supervisor::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip' => $data[2],
                        'phone' => '08' . rand(10, 99) . rand(1000000, 9999999),
                        'institution' => 'Direktorat Sistem Informasi',
                        'status' => 'active',
                    ]
                );
            }

            $supervisors[] = $user;
        }

        $this->command->info('   ✓ ' . count($supervisors) . ' supervisors created');

        return $supervisors;
    }

    private function createInterns(array $supervisors): array
    {
        $interns = [];
        $usedEmails = [];
        $internCount = 20;

        for ($i = 0; $i < $internCount; $i++) {
            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];
            $fullName = $firstName . ' ' . $lastName;

            // Generate unique email
            $email = $this->generateUniqueEmail($firstName, $lastName, $usedEmails);
            $usedEmails[] = $email;

            // Random internship period
            $startDate = Carbon::now()->subMonths(rand(1, 3))->subDays(rand(0, 30));
            $endDate = $startDate->copy()->addMonths(rand(3, 6));

            // Determine status
            $status = $this->determineInternStatus($endDate);

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'password' => Hash::make('password'),
                    'role' => 'intern',
                ]
            );

            $intern = Intern::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nis' => 'NIS' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'school' => $this->schools[array_rand($this->schools)],
                    'department' => $this->departments[array_rand($this->departments)],
                    'phone' => '08' . rand(10, 99) . rand(1000000, 9999999),
                    'address' => 'Jl. ' . $this->lastNames[array_rand($this->lastNames)] . ' No. ' . rand(1, 100) . ', Jakarta',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status,
                    'supervisor_id' => $supervisors[array_rand($supervisors)]->id,
                ]
            );

            $interns[] = $intern;
        }

        $this->command->info('   ✓ ' . $internCount . ' interns created');

        return $interns;
    }

    /*
    |--------------------------------------------------------------------------
    | Task Creation Methods
    |--------------------------------------------------------------------------
    */

    private function createTaskAssignments(User $admin, array $interns): void
    {
        $activeInterns = collect($interns)->filter(fn($i) => $i->status === 'active')->values();
        $allStatuses = ['pending', 'in_progress', 'submitted', 'revision', 'completed'];
        $assignmentCount = 8;

        for ($i = 0; $i < $assignmentCount; $i++) {
            $deadline = Carbon::now()->addDays(rand(-7, 21));

            $taskAssignment = TaskAssignment::create([
                'title' => $this->taskTitles[$i],
                'description' => $this->taskDescriptions[$i],
                'assigned_by' => $admin->id,
                'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                'deadline' => $deadline,
                'deadline_time' => sprintf('%02d:00', rand(14, 18)),
                'start_date' => Carbon::now()->addDays(rand(-3, 7)),
                'assign_to_all' => $i < 2,
            ]);

            // Determine which interns to assign
            $assignedInterns = $i < 2
                ? $activeInterns
                : $activeInterns->random(min(rand(5, 12), $activeInterns->count()));

            $taskAssignment->interns()->attach($assignedInterns->pluck('id'));

            // Create individual tasks for each assigned intern
            foreach ($assignedInterns as $intern) {
                $this->createTask($taskAssignment, $intern, $admin, $deadline, $allStatuses);
            }
        }

        $this->command->info('   ✓ ' . $assignmentCount . ' task assignments created');
    }

    private function createTask(
        TaskAssignment $taskAssignment,
        Intern $intern,
        User $admin,
        Carbon $deadline,
        array $allStatuses
    ): void {
        $status = $allStatuses[rand(0, 4)];
        $taskData = $this->generateTaskData($status, $deadline);

        Task::create([
            'task_assignment_id' => $taskAssignment->id,
            'title' => $taskAssignment->title,
            'description' => $taskAssignment->description,
            'intern_id' => $intern->id,
            'assigned_by' => $admin->id,
            'priority' => $taskAssignment->priority,
            'status' => $status,
            'deadline' => $deadline,
            'deadline_time' => $taskAssignment->deadline_time,
            'start_date' => $taskAssignment->start_date,
            ...$taskData,
        ]);
    }

    private function generateTaskData(string $status, Carbon $deadline): array
    {
        $data = [
            'started_at' => null,
            'submitted_at' => null,
            'completed_at' => null,
            'approved_at' => null,
            'is_late' => false,
            'submission_links' => null,
            'score' => null,
            'admin_feedback' => null,
        ];

        if (in_array($status, ['in_progress', 'submitted', 'revision', 'completed'])) {
            $data['started_at'] = $deadline->copy()->subDays(rand(3, 10));
        }

        if ($status === 'submitted') {
            $data['submitted_at'] = Carbon::now()->subDays(rand(0, 3));
            $data['is_late'] = $data['submitted_at']->isAfter($deadline);
            $data['submission_links'] = $this->generateSubmissionLinks();
        }

        if ($status === 'completed') {
            $data['completed_at'] = Carbon::now()->subDays(rand(0, 7));
            $data['submitted_at'] = $data['completed_at']->copy()->subHours(rand(1, 24));
            $data['approved_at'] = $data['completed_at']->copy()->addHours(rand(1, 48));
            $data['is_late'] = $data['submitted_at']->isAfter($deadline);
            $data['score'] = rand(70, 100);
            $data['admin_feedback'] = $data['score'] >= 85
                ? 'Kerja bagus! Hasilnya sesuai ekspektasi.'
                : 'Sudah cukup baik, perlu sedikit improvement untuk kedepannya.';
            $data['submission_links'] = $this->generateSubmissionLinks();
        }

        if ($status === 'revision') {
            $data['submitted_at'] = Carbon::now()->subDays(rand(1, 3));
            $data['admin_feedback'] = 'Perlu perbaikan pada bagian ' .
                ['UI/UX', 'validasi data', 'error handling', 'dokumentasi'][rand(0, 3)] . '.';
            $data['submission_links'] = $this->generateSubmissionLinks();
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Creation
    |--------------------------------------------------------------------------
    */

    private function createAttendances(array $interns): void
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        foreach ($interns as $intern) {
            if ($intern->status === 'cancelled') continue;

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                if ($date->isWeekend()) continue;

                $attendanceData = $this->generateAttendanceData();

                Attendance::create([
                    'intern_id' => $intern->id,
                    'date' => $date->toDateString(),
                    ...$attendanceData,
                ]);
            }
        }

        $this->command->info('   ✓ Attendance records created (30 days)');
    }

    private function generateAttendanceData(): array
    {
        $rand = rand(1, 100);

        if ($rand <= 75) {
            return [
                'status' => 'present',
                'check_in' => sprintf('%02d:%02d', 8, rand(0, 15)),
                'check_out' => sprintf('%02d:%02d', rand(16, 17), rand(0, 59)),
                'late_reason' => null,
                'notes' => null,
            ];
        }

        if ($rand <= 88) {
            return [
                'status' => 'late',
                'check_in' => sprintf('%02d:%02d', rand(8, 9), rand(16, 59)),
                'check_out' => sprintf('%02d:%02d', rand(16, 17), rand(0, 59)),
                'late_reason' => ['Macet', 'Hujan deras', 'Kendaraan mogok', 'Keperluan keluarga'][rand(0, 3)],
                'notes' => null,
            ];
        }

        if ($rand <= 94) {
            return [
                'status' => 'sick',
                'check_in' => null,
                'check_out' => null,
                'late_reason' => null,
                'notes' => 'Sakit ' . ['flu', 'demam', 'migrain'][rand(0, 2)],
            ];
        }

        if ($rand <= 98) {
            return [
                'status' => 'permission',
                'check_in' => null,
                'check_out' => null,
                'late_reason' => null,
                'notes' => 'Keperluan keluarga',
            ];
        }

        return [
            'status' => 'absent',
            'check_in' => null,
            'check_out' => null,
            'late_reason' => null,
            'notes' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Assessment Creation
    |--------------------------------------------------------------------------
    */

    private function createAssessments(User $admin): void
    {
        $completedTasks = Task::where('status', 'completed')->get();
        $assessmentCount = 0;

        foreach ($completedTasks as $task) {
            if (rand(1, 3) !== 1) continue;

            Assessment::create([
                'intern_id' => $task->intern_id,
                'task_id' => $task->id,
                'assessed_by' => $admin->id,
                'quality_score' => rand(70, 100),
                'speed_score' => rand(65, 100),
                'initiative_score' => rand(60, 100),
                'teamwork_score' => rand(70, 100),
                'communication_score' => rand(65, 100),
                'strengths' => ['Problem solving yang baik', 'Tekun dan teliti', 'Komunikatif', 'Cepat belajar', 'Kreatif'][rand(0, 4)],
                'improvements' => ['Perlu lebih teliti', 'Time management', 'Dokumentasi bisa ditingkatkan', 'Komunikasi lebih aktif'][rand(0, 3)],
                'comments' => 'Secara keseluruhan menunjukkan perkembangan yang ' . ['baik', 'cukup baik', 'sangat baik'][rand(0, 2)] . '.',
            ]);

            $assessmentCount++;
        }

        $this->command->info('   ✓ ' . $assessmentCount . ' assessments created');
    }

    /*
    |--------------------------------------------------------------------------
    | Report Creation
    |--------------------------------------------------------------------------
    */

    private function createReports(array $interns): void
    {
        $reportCount = 0;

        foreach ($interns as $intern) {
            if ($intern->status === 'cancelled') continue;

            $weeksCount = rand(2, 4);
            for ($w = 1; $w <= $weeksCount; $w++) {
                $periodStart = Carbon::now()->subWeeks($w)->startOfWeek();
                $periodEnd = $periodStart->copy()->endOfWeek();

                Report::create([
                    'intern_id' => $intern->id,
                    'created_by' => $intern->user_id,
                    'title' => 'Laporan Mingguan - Minggu ke-' . $w,
                    'content' => 'Selama minggu ini saya telah mengerjakan beberapa tugas yang diberikan. ' .
                        'Berikut adalah ringkasan pekerjaan yang telah diselesaikan...',
                    'type' => 'weekly',
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'status' => ['draft', 'submitted', 'reviewed'][rand(0, 2)],
                    'feedback' => rand(0, 1) ? 'Laporan sudah cukup lengkap. Teruskan!' : null,
                ]);

                $reportCount++;
            }
        }

        $this->command->info('   ✓ ' . $reportCount . ' reports created');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    private function generateUniqueEmail(string $firstName, string $lastName, array $usedEmails): string
    {
        $emailBase = strtolower($firstName . '.' . $lastName);
        $email = $emailBase . '@student.id';
        $counter = 1;

        while (in_array($email, $usedEmails)) {
            $email = $emailBase . $counter . '@student.id';
            $counter++;
        }

        return $email;
    }

    private function determineInternStatus(Carbon $endDate): string
    {
        if ($endDate->isPast()) {
            return 'completed';
        }

        return rand(1, 20) === 1 ? 'cancelled' : 'active';
    }

    private function generateSubmissionLinks(): string
    {
        return json_encode([
            'github' => 'https://github.com/user/project-' . rand(100, 999),
            'demo' => 'https://demo.example.com/project-' . rand(100, 999),
        ]);
    }

    private function printSummary(): void
    {
        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════╗');
        $this->command->info('║          Demo Account Credentials            ║');
        $this->command->info('╠══════════════════════════════════════════════╣');
        $this->command->info('║  Admin:                                      ║');
        $this->command->info('║    Email: admin@internhub.id                 ║');
        $this->command->info('║    Pass:  password                           ║');
        $this->command->info('║                                              ║');
        $this->command->info('║  Supervisor:                                 ║');
        $this->command->info('║    Email: bambang.suryadi@internhub.id       ║');
        $this->command->info('║    Pass:  password                           ║');
        $this->command->info('║                                              ║');
        $this->command->info('║  Intern:                                     ║');
        $this->command->info('║    Email: (any intern email)                 ║');
        $this->command->info('║    Pass:  password                           ║');
        $this->command->info('╚══════════════════════════════════════════════╝');
    }
}
