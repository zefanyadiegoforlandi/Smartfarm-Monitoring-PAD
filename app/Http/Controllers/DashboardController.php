<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use GuzzleHttp\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $token = session('jwt');
        
        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Silakan login terlebih dahulu.');
        }

        $response = Http::withToken($token)->get(env('API_BASE_URL'));
        if ($response->successful()) {
            $apiData = $response->json();
            $users = collect(json_decode(json_encode($apiData['users']), false))
                ->where('level', 'user')
                ->sortByDesc('id'); 
            $totalUsers = $users->count();  // Menghitung total user

            $lahan = collect(json_decode(json_encode($apiData['lahan']), false));
            $totalLahan = $lahan->count();  // Menghitung total lahan

            $sensor = collect(json_decode(json_encode($apiData['sensor']), false));
            $totalSensors = $sensor->count();  // Menghitung total sensor

            $perPage = 5; 
            $currentPage = request()->input('page', 1); 
            $paginator = new LengthAwarePaginator(
                $users->forPage($currentPage, $perPage), 
                $users->count(), 
                $perPage, 
                $currentPage 
            );
            $paginator->setPath(request()->url());

            foreach ($users as $user) {
                $userLahanIds = $lahan->where('id_user', $user->id)->pluck('id_lahan');
                $totalUniqueSensors = $sensor->whereIn('id_lahan', $userLahanIds)->unique('id_sensor')->count();
                $user->totalUniqueSensors = $totalUniqueSensors;
            }

            return view('pages/dashboard/admin-dashboard', [
                'paginator' => $paginator, 
                'totalUsers' => $totalUsers, 
                'totalLahan' => $totalLahan, 
                'totalSensors' => $totalSensors
            ]);
        }
    }

    public function daftar_farmer()
    {
        $token = session('jwt');
        
        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Silakan login terlebih dahulu.');
        }
    
        $response = Http::withToken($token)->get(env('API_BASE_URL'));
        if ($response->successful()) {
            $apiData = $response->json();
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
            $lahan = collect(json_decode(json_encode($apiData['lahan']), false));
            $sensor = collect(json_decode(json_encode($apiData['sensor']), false));

            foreach ($users as $user) {
                $userLahanIds = collect($lahan)->where('id_user', $user->id)->pluck('id_lahan');
                $totalUniqueSensors = collect($sensor)->whereIn('id_lahan', $userLahanIds)->unique('id_sensor')->count();
                $user->totalUniqueSensors = $totalUniqueSensors;
            }

            return view('pages/dashboard/dashboard', compact('paginator', 'sensor'));
        }
    }
}
