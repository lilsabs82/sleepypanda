<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\SleepRecord;
use App\Models\InsomniaAlert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SleepDataSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Alfonso de', 'Maria Santos', 'John Doe', 'Sarah Lee', 'Michael Chen',
            'Emma Wilson', 'David Kim', 'Lisa Park', 'James Brown', 'Anna Garcia',
            'Robert Taylor', 'Jennifer Martinez', 'William Anderson', 'Elizabeth Thomas',
            'Christopher Jackson', 'Jessica White', 'Daniel Harris', 'Ashley Martin',
            'Matthew Thompson', 'Amanda Robinson', 'Andrew Clark', 'Stephanie Lewis',
            'Joshua Walker', 'Nicole Hall', 'Kevin Allen', 'Michelle Young', 'Brian King',
            'Kimberly Wright', 'Ryan Scott', 'Laura Green'
        ];

        $genders = ['male', 'female'];
        $statuses = ['active', 'active', 'active', 'inactive']; // 75% active

        // Create 50 users with profiles
        for ($i = 0; $i < 50; $i++) {
            $user = User::create([
                'email' => 'user' . ($i + 1) . '@example.com',
                'hashed_password' => Hash::make('password123'),
            ]);

            $gender = $genders[array_rand($genders)];
            $status = $statuses[array_rand($statuses)];

            UserProfile::create([
                'user_id' => $user->id,
                'name' => $names[$i % count($names)],
                'phone' => '+62' . rand(100000000, 999999999),
                'gender' => $gender,
                'status' => $status,
                'last_active_at' => Carbon::now()->subHours(rand(1, 72)),
            ]);

            // Create sleep records for last 60 days
            for ($day = 0; $day < 60; $day++) {
                $date = Carbon::now()->subDays($day);

                // Random sleep data
                $sleepHour = rand(21, 23);
                $sleepMinute = rand(0, 59);
                $duration = rand(180, 540); // 3-9 hours
                $isInsomnia = rand(1, 10) <= 2; // 20% chance of insomnia

                if ($isInsomnia) {
                    $duration = rand(30, 180); // 0.5-3 hours for insomnia
                }

                $sleepStart = sprintf('%02d:%02d:00', $sleepHour, $sleepMinute);
                $wakeHour = ($sleepHour + floor($duration / 60)) % 24;
                $wakeMinute = ($sleepMinute + ($duration % 60)) % 60;
                $sleepEnd = sprintf('%02d:%02d:00', $wakeHour, $wakeMinute);

                SleepRecord::create([
                    'user_id' => $user->id,
                    'sleep_date' => $date->format('Y-m-d'),
                    'sleep_start' => $sleepStart,
                    'sleep_end' => $sleepEnd,
                    'duration_minutes' => $duration,
                    'quality_percent' => $isInsomnia ? rand(10, 40) : rand(60, 95),
                    'is_insomnia' => $isInsomnia,
                    'time_to_sleep_minutes' => $isInsomnia ? rand(60, 180) : rand(10, 45),
                ]);

                // Create insomnia alert for insomnia cases
                if ($isInsomnia && rand(1, 3) == 1) {
                    InsomniaAlert::create([
                        'user_id' => $user->id,
                        'hours_without_sleep' => rand(20, 40),
                        'avg_duration_minutes' => rand(60, 120),
                        'alert_date' => $date,
                    ]);
                }
            }
        }
    }
}
