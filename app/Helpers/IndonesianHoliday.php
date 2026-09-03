<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IndonesianHoliday
{
    // Cache for 24 hours
    const CACHE_DURATION = 86400; // seconds
    const API_URL = 'https://api-harilibur.vercel.app/api';

    /**
     * Get Indonesian national holidays from API with caching.
     * 
     * @param int $year
     * @return array ['Y-m-d' => 'Holiday Name']
     */
    public static function getHolidays(int $year = null): array
    {
        $cacheKey = 'indonesian_holidays_' . ($year ?? 'all');
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($year) {
            try {
                $response = Http::timeout(10)->get(self::API_URL);
                
                if ($response->successful()) {
                    $holidays = [];
                    $data = $response->json();
                    
                    foreach ($data as $item) {
                        // Only include national holidays
                        if (!$item['is_national_holiday']) {
                            continue;
                        }
                        
                        $date = $item['holiday_date'];
                        // Normalize date format (API sometimes returns 2026-1-1 instead of 2026-01-01)
                        $carbonDate = Carbon::parse($date);
                        
                        // Filter by year if specified
                        if ($year && $carbonDate->year != $year) {
                            continue;
                        }
                        
                        $holidays[$carbonDate->format('Y-m-d')] = $item['holiday_name'];
                    }
                    
                    return $holidays;
                }
                
                Log::warning('Holiday API returned non-successful response', ['status' => $response->status()]);
                return self::getFallbackHolidays($year);
                
            } catch (\Exception $e) {
                Log::error('Holiday API error', ['error' => $e->getMessage()]);
                return self::getFallbackHolidays($year);
            }
        });
    }

    /**
     * Fallback holidays if API fails.
     */
    private static function getFallbackHolidays(int $year = null): array
    {
        $year = $year ?? now()->year;
        
        return [
            "{$year}-01-01" => 'Tahun Baru Masehi',
            "{$year}-05-01" => 'Hari Buruh Internasional',
            "{$year}-06-01" => 'Hari Lahir Pancasila',
            "{$year}-08-17" => 'Hari Kemerdekaan RI',
            "{$year}-12-25" => 'Hari Raya Natal',
        ];
    }

    /**
     * Get holidays for a specific month.
     * 
     * @param int $year
     * @param int $month
     * @return array [day => 'Holiday Name']
     */
    public static function getMonthHolidays(int $year, int $month): array
    {
        $allHolidays = self::getHolidays();
        $monthHolidays = [];

        foreach ($allHolidays as $date => $name) {
            $carbonDate = Carbon::parse($date);
            if ($carbonDate->year == $year && $carbonDate->month == $month) {
                $monthHolidays[$carbonDate->day] = $name;
            }
        }

        // Sort by day
        ksort($monthHolidays);

        return $monthHolidays;
    }

    /**
     * Check if a date is a holiday.
     */
    public static function isHoliday(string $date): bool
    {
        $carbonDate = Carbon::parse($date);
        $holidays = self::getHolidays($carbonDate->year);
        return isset($holidays[$carbonDate->format('Y-m-d')]);
    }

    /**
     * Get holiday name for a date.
     */
    public static function getHolidayName(string $date): ?string
    {
        $carbonDate = Carbon::parse($date);
        $holidays = self::getHolidays($carbonDate->year);
        return $holidays[$carbonDate->format('Y-m-d')] ?? null;
    }

    /**
     * Check if a day is Sunday.
     */
    public static function isSunday(int $year, int $month, int $day): bool
    {
        $date = Carbon::createFromDate($year, $month, $day);
        return $date->isSunday();
    }

    /**
     * Clear holiday cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('indonesian_holidays_all');
        for ($year = 2024; $year <= 2030; $year++) {
            Cache::forget('indonesian_holidays_' . $year);
        }
    }
}
