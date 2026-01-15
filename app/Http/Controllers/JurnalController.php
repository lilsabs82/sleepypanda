<?php

namespace App\Http\Controllers;

use App\Models\SleepRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JurnalController extends Controller
{
    public function daily(Request $request)
    {
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            $records = SleepRecord::whereDate('sleep_date', $date)->get();

            $userCount = $records->count();
            $avgDuration = $records->avg('duration_minutes') ?? 0;
            $avgStart = $this->calculateAvgTime($records, 'sleep_start');
            $avgEnd = $this->calculateAvgTime($records, 'sleep_end');

            $dates[] = [
                'label' => $date->format('d F Y'),
                'date' => $date->format('Y-m-d'),
                'stats' => [
                    'user' => $userCount,
                    'durasi' => $this->formatDuration($avgDuration),
                    'waktu' => $avgStart . ' - ' . $avgEnd,
                ],
                'chart' => $this->getHourlyData($date),
            ];
        }

        return response()->json([
            'dates' => $dates,
            'chartLabels' => ['0j', '2j', '4j', '6j', '8j'],
        ]);
    }

    public function weekly(Request $request)
    {
        $weeks = [];
        for ($i = 0; $i < 4; $i++) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();

            $records = SleepRecord::whereBetween('sleep_date', [$startDate, $endDate])->get();

            $userCount = $records->groupBy('user_id')->count();
            $avgDuration = $records->avg('duration_minutes') ?? 0;
            $totalDuration = $records->sum('duration_minutes');
            $avgStart = $this->calculateAvgTime($records, 'sleep_start');
            $avgEnd = $this->calculateAvgTime($records, 'sleep_end');

            $weeks[] = [
                'label' => $startDate->format('d F') . ' - ' . $endDate->format('d F Y'),
                'stats' => [
                    'user' => $userCount,
                    'avgDurasi' => $this->formatDuration($avgDuration),
                    'totalDurasi' => $this->formatDuration($totalDuration),
                    'mulaiTidur' => $avgStart,
                    'bangunTidur' => $avgEnd,
                ],
                'chart' => $this->getWeeklyChartData($startDate, $endDate),
            ];
        }

        return response()->json([
            'weeks' => $weeks,
            'chartLabels' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        ]);
    }

    public function monthly(Request $request)
    {
        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $records = SleepRecord::whereYear('sleep_date', $date->year)
                ->whereMonth('sleep_date', $date->month)->get();

            $userCount = $records->groupBy('user_id')->count();
            $avgDuration = $records->avg('duration_minutes') ?? 0;
            $totalDuration = $records->sum('duration_minutes');
            $avgStart = $this->calculateAvgTime($records, 'sleep_start');
            $avgEnd = $this->calculateAvgTime($records, 'sleep_end');

            $months[] = [
                'label' => $date->format('F Y'),
                'stats' => [
                    'user' => $userCount,
                    'avgDurasi' => $this->formatDuration($avgDuration),
                    'totalDurasi' => $this->formatDuration($totalDuration),
                    'mulaiTidur' => $avgStart,
                    'bangunTidur' => $avgEnd,
                ],
                'chart' => $this->getMonthlyChartData($date),
            ];
        }

        return response()->json([
            'months' => $months,
            'chartLabels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        ]);
    }

    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return "{$hours} jam {$mins} menit";
    }

    private function calculateAvgTime($records, $field)
    {
        if ($records->isEmpty()) return '00:00';

        $totalMinutes = 0;
        foreach ($records as $record) {
            $time = Carbon::parse($record->$field);
            $totalMinutes += $time->hour * 60 + $time->minute;
        }
        $avgMinutes = $totalMinutes / $records->count();
        $hours = floor($avgMinutes / 60) % 24;
        $minutes = $avgMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    private function getHourlyData($date)
    {
        $data = [];
        $hours = [0, 2, 4, 6, 8];
        foreach ($hours as $hour) {
            $count = SleepRecord::whereDate('sleep_date', $date)
                ->whereRaw('HOUR(sleep_start) >= ? AND HOUR(sleep_start) < ?', [$hour, $hour + 2])
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getWeeklyChartData($startDate, $endDate)
    {
        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $avgHours = SleepRecord::whereDate('sleep_date', $date)->avg('duration_minutes') ?? 0;
            $data[] = round($avgHours / 60, 1);
        }
        return $data;
    }

    private function getMonthlyChartData($monthDate)
    {
        $data = [];
        for ($week = 1; $week <= 4; $week++) {
            $startDay = ($week - 1) * 7 + 1;
            $endDay = min($week * 7, $monthDate->daysInMonth);

            $avgHours = SleepRecord::whereYear('sleep_date', $monthDate->year)
                ->whereMonth('sleep_date', $monthDate->month)
                ->whereDay('sleep_date', '>=', $startDay)
                ->whereDay('sleep_date', '<=', $endDay)
                ->avg('duration_minutes') ?? 0;
            $data[] = round($avgHours / 60, 1);
        }
        return $data;
    }
}
