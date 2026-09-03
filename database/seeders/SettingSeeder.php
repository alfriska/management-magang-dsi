<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Application settings configuration.
     *
     * These settings can be modified via the admin panel.
     */
    private array $settings = [
        // Office Hours
        [
            'key' => 'office_start_time',
            'value' => '08:00',
            'type' => 'time',
            'description' => 'Jam masuk kantor',
        ],
        [
            'key' => 'late_tolerance_time',
            'value' => '08:15',
            'type' => 'time',
            'description' => 'Batas toleransi terlambat',
        ],
        [
            'key' => 'office_end_time',
            'value' => '16:00',
            'type' => 'time',
            'description' => 'Jam pulang kantor',
        ],

        // Office Location (DSI Office coordinates)
        [
            'key' => 'office_latitude',
            'value' => '-7.035485731957248',
            'type' => 'string',
            'description' => 'Latitude lokasi kantor',
        ],
        [
            'key' => 'office_longitude',
            'value' => '110.47464898344766',
            'type' => 'string',
            'description' => 'Longitude lokasi kantor',
        ],
        [
            'key' => 'max_checkin_distance',
            'value' => '100',
            'type' => 'integer',
            'description' => 'Jarak maksimal presensi masuk (meter)',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📋 Seeding application settings...');

        foreach ($this->settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('   ✓ ' . count($this->settings) . ' settings configured');
    }
}
