<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class DashboardUserController extends Controller
{
    public function index()
    {
        try {
            $token = session('jwt');
            
            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->get("http://localhost/smartfarm_jwt/");
            if ($response->successful()) {
                $apiData = $response->json();

                // Ambil data pengguna (users)
                $users = collect(json_decode(json_encode($apiData['users']), false))
                    ->where('level', 'user')
                    ->sortByDesc('id');

                $perPage = 5;
                $currentPage = request()->input('page', 1);
                $paginator = new LengthAwarePaginator(
                    $users->forPage($currentPage, $perPage),
                    $users->count(),
                    $perPage,
                    $currentPage
                );
                $paginator->setPath(request()->url());

                $user_id = session('user_id');
                $lahan = collect(json_decode(json_encode($apiData['lahan']), false))
                    ->where('id_user', $user_id);

                Log::info('Filtered Lahan:', $lahan->toArray());

                $nama_lahan = $lahan->where('id_lahan', session('id_lahan'))->first()->nama_lahan ?? '';

                $sensor = collect(json_decode(json_encode($apiData['sensor']), false));

                foreach ($users as $user) {
                    $userLahanIds = $lahan->where('id_user', $user->id)->pluck('id_lahan');
                    $totalUniqueSensors = $sensor->whereIn('id_lahan', $userLahanIds)->unique('id_sensor')->count();
                    $user->totalUniqueSensors = $totalUniqueSensors;
                }

                return view('/pages/dashboard/user-dashboard', compact('paginator', 'nama_lahan', 'lahan', 'sensor'));
            } else {
                throw new \Exception('Gagal mengambil data dari API.');
            }

        } catch (\Exception $e) {
            Log::error('Error in DashboardUserController@index: ' . $e->getMessage());
            return redirect('/')->withErrors('Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}

