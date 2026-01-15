<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\SleepRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalUsers = User::count();
        $femaleUsers = UserProfile::where('gender', 'female')->count();
        $maleUsers = UserProfile::where('gender', 'male')->count();

        $avgTime = SleepRecord::avg('duration_minutes') ?? 0;

        return response()->json([
            'total_users' => $totalUsers,
            'female_users' => $femaleUsers,
            'male_users' => $maleUsers,
            'average_time' => round($avgTime / 60, 2),
        ]);
    }

    public function dailyReport()
    {
        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $female = [];
        $male = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $femaleCount = SleepRecord::whereDate('sleep_date', $date)
                ->whereHas('user.profile', fn($q) => $q->where('gender', 'female'))
                ->count();

            $maleCount = SleepRecord::whereDate('sleep_date', $date)
                ->whereHas('user.profile', fn($q) => $q->where('gender', 'male'))
                ->count();

            $female[] = $femaleCount;
            $male[] = $maleCount;
        }

        return response()->json([
            'labels' => $days,
            'female' => $female,
            'male' => $male,
        ]);
    }

    public function weeklyReport()
    {
        $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        $female = [];
        $male = [];

        for ($week = 3; $week >= 0; $week--) {
            $startDate = Carbon::now()->subWeeks($week)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($week)->endOfWeek();

            $femaleCount = SleepRecord::whereBetween('sleep_date', [$startDate, $endDate])
                ->whereHas('user.profile', fn($q) => $q->where('gender', 'female'))
                ->count();

            $maleCount = SleepRecord::whereBetween('sleep_date', [$startDate, $endDate])
                ->whereHas('user.profile', fn($q) => $q->where('gender', 'male'))
                ->count();

            $female[] = $femaleCount;
            $male[] = $maleCount;
        }

        return response()->json([
            'labels' => $labels,
            'female' => $female,
            'male' => $male,
        ]);
    }

    public function monthlyReport()
    {
        $labels = [];
        $female = [];
        $male = [];

        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');

            $femaleCount = SleepRecord::whereYear('sleep_date', $date->year)
                ->whereMonth('sleep_date', $date->month)
                ->whereHas('user.profile', fn($q) => $q->where('gender', 'female'))
                ->count();

            $maleCount = SleepRecord::whereYear('sleep_date', $date->year)
                ->whereMonth('sleep_date', $date->month)
                ->whereHas('user.profile', fn($q) => $q->where('gender', 'male'))
                ->count();

            $female[] = $femaleCount;
            $male[] = $maleCount;
        }

        return response()->json([
            'labels' => $labels,
            'female' => $female,
            'male' => $male,
        ]);
    }

    public function sleepTimeChart()
    {
        $labels = [];
        $female = [];
        $male = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');

            $femaleAvg = SleepRecord::whereDate('sleep_date', $date)
                ->whereHas('user.profile', fn($q) => $q->where('gender', 'female'))
                ->avg('duration_minutes') ?? 0;

            $maleAvg = SleepRecord::whereDate('sleep_date', $date)
                ->whereHas('user.profile', fn($q) => $q->where('gender', 'male'))
                ->avg('duration_minutes') ?? 0;

            $female[] = round($femaleAvg / 60, 1);
            $male[] = round($maleAvg / 60, 1);
        }

        return response()->json([
            'labels' => $labels,
            'female' => $female,
            'male' => $male,
        ]);
    }
}
