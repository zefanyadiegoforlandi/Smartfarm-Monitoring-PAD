<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class DownloadDataController extends Controller
{
    public function index()
    {
        try {
            $token = session('jwt');
            $id_lahan = session('id_lahan');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Silakan login terlebih dahulu.');
            }

            if (!$id_lahan) {
                return redirect('/')->withErrors('ID Lahan tidak ditemukan. Silakan login ulang.');
            }

            $response = Http::withToken($token)->get(env('SENSOR_URL'));
            if ($response->successful()) {
                $sensorData = $response->json();

                $sensors = array_filter($sensorData, function ($sensor) use ($id_lahan) {
                    return $sensor['id_lahan'] == $id_lahan;
                });

                return view('/user/download-data', compact('sensors'));
            } else {
                return redirect('/')->withErrors('Terjadi kesalahan. Silakan coba lagi.');
            }

        } catch (\Exception $e) {
            Log::error('Error in DownloadDataController@index: ' . $e->getMessage());
            return redirect('/')->withErrors('Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}
