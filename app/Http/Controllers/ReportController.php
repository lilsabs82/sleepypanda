<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SleepRecord;
use App\Models\InsomniaAlert;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            $records = SleepRecord::whereDate('sleep_date', $date)->get();
            $insomniaRecords = $records->where('is_insomnia', true);

            $dates[] = [
                'label' => $date->format('d F Y'),
                'stats' => [
                    'totalUsers' => $records->groupBy('user_id')->count(),
                    'insomniaCases' => $insomniaRecords->count(),
                    'timeToSleep' => round($records->avg('time_to_sleep_minutes') ?? 0) . ' min',
                    'avgSleepTime' => round(($records->avg('duration_minutes') ?? 0) / 60, 1) . ' h',
                ],
                'chart' => $this->getTimeDistribution($date),
                'alerts' => $this->getAlerts($date, $date),
            ];
        }

        return response()->json([
            'title' => 'Report Insomnia daily',
            'dates' => $dates,
            'chartLabels' => ['22:00', '23:00', '00:00', '01:00', '02:00', '03:00'],
        ]);
    }

    public function weekly(Request $request)
    {
        $weeks = [];
        for ($i = 0; $i < 4; $i++) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();

            $records = SleepRecord::whereBetween('sleep_date', [$startDate, $endDate])->get();
            $insomniaRecords = $records->where('is_insomnia', true);

            $weeks[] = [
                'label' => $startDate->format('d F') . ' - ' . $endDate->format('d F Y'),
                'stats' => [
                    'totalUsers' => $records->groupBy('user_id')->count(),
                    'insomniaCases' => $insomniaRecords->count(),
                    'timeToSleep' => round($records->avg('time_to_sleep_minutes') ?? 0) . ' min',
                    'avgSleepTime' => round(($records->avg('duration_minutes') ?? 0) / 60, 1) . ' h',
                ],
                'chart' => $this->getWeekTimeDistribution($startDate, $endDate),
                'alerts' => $this->getAlerts($startDate, $endDate),
            ];
        }

        return response()->json([
            'title' => 'Report Insomnia Weekly',
            'weeks' => $weeks,
            'chartLabels' => ['22:00', '23:00', '00:00', '01:00', '02:00', '03:00'],
        ]);
    }

    public function monthly(Request $request)
    {
        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();

            $records = SleepRecord::whereBetween('sleep_date', [$startDate, $endDate])->get();
            $insomniaRecords = $records->where('is_insomnia', true);

            $months[] = [
                'label' => $date->format('F Y'),
                'stats' => [
                    'totalUsers' => $records->groupBy('user_id')->count(),
                    'insomniaCases' => $insomniaRecords->count(),
                    'timeToSleep' => round($records->avg('time_to_sleep_minutes') ?? 0) . ' min',
                    'avgSleepTime' => round(($records->avg('duration_minutes') ?? 0) / 60, 1) . ' h',
                ],
                'chart' => $this->getMonthTimeDistribution($startDate, $endDate),
                'alerts' => $this->getAlerts($startDate, $endDate),
            ];
        }

        return response()->json([
            'title' => 'Report Insomnia monthly',
            'months' => $months,
            'chartLabels' => ['22:00', '23:00', '00:00', '01:00', '02:00', '03:00'],
        ]);
    }

    private function getTimeDistribution($date)
    {
        $hours = [22, 23, 0, 1, 2, 3];
        $data = [];
        foreach ($hours as $hour) {
            $count = SleepRecord::whereDate('sleep_date', $date)
                ->where('is_insomnia', true)
                ->whereRaw('HOUR(sleep_start) = ?', [$hour])
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getWeekTimeDistribution($startDate, $endDate)
    {
        $hours = [22, 23, 0, 1, 2, 3];
        $data = [];
        foreach ($hours as $hour) {
            $count = SleepRecord::whereBetween('sleep_date', [$startDate, $endDate])
                ->where('is_insomnia', true)
                ->whereRaw('HOUR(sleep_start) = ?', [$hour])
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getMonthTimeDistribution($startDate, $endDate)
    {
        $hours = [22, 23, 0, 1, 2, 3];
        $data = [];
        foreach ($hours as $hour) {
            $count = SleepRecord::whereBetween('sleep_date', [$startDate, $endDate])
                ->where('is_insomnia', true)
                ->whereRaw('HOUR(sleep_start) = ?', [$hour])
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getAlerts($startDate, $endDate)
    {
        return InsomniaAlert::with('user.profile')
            ->whereBetween('alert_date', [$startDate, $endDate])
            ->orderBy('alert_date', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($alert) {
                return [
                    'date' => Carbon::parse($alert->alert_date)->format('d F Y'),
                    'timeAgo' => Carbon::parse($alert->alert_date)->diffForHumans(),
                    'userId' => '#' . $alert->user_id,
                    'duration' => $alert->formatted_duration,
                    'noSleep' => $alert->hours_without_sleep . ' jam',
                ];
            });
    }
}
