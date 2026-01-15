<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\SleepRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function stats()
    {
        $totalUsers = User::count();
        $activeUsers = UserProfile::where('status', 'active')->count();
        $newUsers = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $inactiveUsers = UserProfile::where('status', 'inactive')->count();

        return response()->json([
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users' => $newUsers,
            'inactive_users' => $inactiveUsers,
        ]);
    }

    public function index(Request $request)
    {
        $query = User::with('profile');

        // Search
        $search = $request->get('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('profile', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');

        if ($sortBy === 'name') {
            $query->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                  ->orderBy('user_profiles.name', $sortOrder)
                  ->select('users.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->paginate(20);

        $usersData = $users->map(function ($user) {
            $profile = $user->profile;
            $recentSleep = SleepRecord::where('user_id', $user->id)
                ->orderBy('sleep_date', 'desc')
                ->limit(7)
                ->get();

            $avgSleep = $recentSleep->avg('duration_minutes') ?? 0;
            $avgQuality = $recentSleep->avg('quality_percent') ?? 0;

            return [
                'id' => $user->id,
                'name' => $profile->name ?? 'User',
                'email' => $user->email,
                'phone' => $profile->phone ?? '-',
                'avgSleep' => round($avgSleep / 60, 1) . 'h',
                'quality' => round($avgQuality) . '%',
                'status' => $profile->status ?? 'active',
                'lastActive' => $profile && $profile->last_active_at
                    ? Carbon::parse($profile->last_active_at)->format('Y-m-d')
                    : '-',
                'lastTime' => $profile && $profile->last_active_at
                    ? Carbon::parse($profile->last_active_at)->format('H:i')
                    : '-',
            ];
        });

        return response()->json([
            'users' => $usersData,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Update email if provided
        if ($request->has('email') && $request->email !== $user->email) {
            $existing = User::where('email', $request->email)->where('id', '!=', $id)->first();
            if ($existing) {
                return response()->json(['message' => 'Email sudah digunakan'], 422);
            }
            $user->email = $request->email;
            $user->save();
        }

        // Update profile
        $profile = UserProfile::where('user_id', $id)->first();
        if ($profile) {
            if ($request->has('name')) {
                $profile->name = $request->name;
            }
            if ($request->has('phone')) {
                $profile->phone = $request->phone;
            }
            $profile->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diupdate'
        ]);
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $generatedPassword = null;

        if ($request->has('password') && $request->password) {
            if (strlen($request->password) < 8) {
                return response()->json(['message' => 'Password minimal 8 karakter'], 422);
            }
            $user->hashed_password = Hash::make($request->password);
        } else {
            // Generate random password
            $generatedPassword = Str::random(10);
            $user->hashed_password = Hash::make($generatedPassword);
        }

        $user->save();

        $response = [
            'success' => true,
            'message' => 'Password berhasil direset'
        ];

        if ($generatedPassword) {
            $response['generated_password'] = $generatedPassword;
        }

        return response()->json($response);
    }
}
