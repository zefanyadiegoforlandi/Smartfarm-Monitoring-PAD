<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DataPertinjauController extends Controller
{
    // Metode untuk menampilkan view utama dan mengirimkan data
    public function showPreview()
    {
        // Mendapatkan id_lahan, token, dan timeFrame dari session
        $id_lahan = session('id_lahan');
        $token = session('jwt');
        $timeFrame = session('timeFrame', 'all'); 

        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
        }
        $response = Http::withToken($token)->get(env('API_BASE_URL'));
    
        if ($response->successful()) {
            $data = $response->json();
    
            // Mendapatkan sensor yang memiliki id_lahan yang sama dengan id_lahan dari session
            $sensors = collect($data['sensor'])->filter(function ($sensor) use ($id_lahan) {
                return $sensor['id_lahan'] == $id_lahan;
            });
    
            if ($sensors->isEmpty()) {
                return view('pertinjau', ['sensorData' => []]);
            }
    
            $user_id = session('user_id');
            $lahan = collect(json_decode(json_encode($data['lahan']), false))
                ->where('id_user', $user_id);
    
            $id_sensors = $sensors->pluck('id_sensor');
    
            $sensorDataResponse = Http::get(env('DATA_SENSOR_URL'));
    
            if ($sensorDataResponse->successful()) {
                $sensorData = $sensorDataResponse->json();
    
                $filteredData = collect($sensorData)->filter(function ($data) use ($id_sensors, $timeFrame) {
                    if (!in_array($data['id_sensor'], $id_sensors->toArray())) {
                        return false;
                    }
                
                    $timeAdded = strtotime($data['TimeAdded']);
                    $now = time();
                
                    // Pastikan data tidak di masa depan
                    if ($timeAdded > $now) {
                        return false;
                    }
                
                    switch ($timeFrame) {
                        case '1day':
                            return $timeAdded >= strtotime('-1 day', $now);
                        case '1week':
                            return $timeAdded >= strtotime('-1 week', $now);
                        case '1month':
                            return $timeAdded >= strtotime('-1 month', $now);
                        default:
                            return true; // Untuk 'all'
                    }
                })->toArray();
                

                // Mendapatkan data terakhir (current) tanpa filter
                $latestData = collect($sensorData)->filter(function ($data) use ($id_sensors) {
                    return in_array($data['id_sensor'], $id_sensors->toArray());
                })->sortByDesc('TimeAdded')->first();
    
                // Hitung nilai current, max, min, dan average untuk setiap parameter
                $parameters = ['Light', 'PersentaseKelembapanTanah', 'AirQuality', 'RainDrop', 'H', 'Temperature', 'Pressure', 'ApproxAltitude'];
                $results = [];
    
                foreach ($parameters as $param) {
                    $values = array_column($filteredData, $param);
                    $results[$param] = [
                        'current' => $latestData ? $latestData[$param] : 0,
                        'max' => !empty($values) ? max($values) : 0,
                        'min' => !empty($values) ? min($values) : 0,
                        'average' => !empty($values) ? number_format(array_sum($values) / count($values), 2) : 0,
                    ];
                }
    
                return view('/user/pertinjau', ['sensorData' => $results, 'lahan' => $lahan]);
            } else {
                return response()->json(['error' => 'Gagal mengambil data dari server'], $response->status());
            }
        } else {
            return response()->json(['error' => 'Gagal mengambil data dari server'], $response->status());
        }
    }
    

    public function getData()
    {
        $id_lahan = session('id_lahan');
        $token = session('jwt');
        $timeFrame = session('timeFrame', 'all'); 

        if (!$id_lahan) {
            return redirect('/')->withErrors('ID Lahan tidak ditemukan. Silakan login ulang.');
        }

        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
        }
    
        $response = Http::withToken($token)->get(env('API_BASE_URL'));
    
        if ($response->successful()) {
            $data = $response->json();
    
            // Mendapatkan sensor yang memiliki id_lahan yang sama dengan id_lahan dari session
            $sensors = collect($data['sensor'])->filter(function ($sensor) use ($id_lahan) {
                return $sensor['id_lahan'] == $id_lahan;
            });
    
            if ($sensors->isEmpty()) {
                return view('pertinjau', ['sensorData' => []]);
            }
    
            $user_id = session('user_id');
            $lahan = collect(json_decode(json_encode($data['lahan']), false))
                ->where('id_user', $user_id);
    
            $id_sensors = $sensors->pluck('id_sensor');
    
            $sensorDataResponse = Http::get(env('DATA_SENSOR_URL'));
    
            if ($sensorDataResponse->successful()) {
                $sensorData = $sensorDataResponse->json();
    
                // Filter data berdasarkan id_sensor dan timeFrame
                $filteredData = collect($sensorData)->filter(function ($data) use ($id_sensors, $timeFrame) {
                    if (!in_array($data['id_sensor'], $id_sensors->toArray())) {
                        return false;
                    }
                
                    $timeAdded = strtotime($data['TimeAdded']);
                    $now = time();
                
                    // Pastikan data tidak di masa depan
                    if ($timeAdded > $now) {
                        return false;
                    }
                
                    switch ($timeFrame) {
                        case '1day':
                            return $timeAdded >= strtotime('-1 day', $now);
                        case '1week':
                            return $timeAdded >= strtotime('-1 week', $now);
                        case '1month':
                            return $timeAdded >= strtotime('-1 month', $now);
                        default:
                            return true; // Untuk 'all'
                    }
                })->toArray();
                
                // Mendapatkan data terakhir (current) tanpa filter
                $latestData = collect($sensorData)->filter(function ($data) use ($id_sensors) {
                    return in_array($data['id_sensor'], $id_sensors->toArray());
                })->sortByDesc('TimeAdded')->first();
    
                // Hitung nilai current, max, min, dan average untuk setiap parameter
                $parameters = ['Light', 'PersentaseKelembapanTanah', 'AirQuality', 'RainDrop', 'H', 'Temperature', 'Pressure', 'ApproxAltitude'];
                $results = [];
    
                foreach ($parameters as $param) {
                    $values = array_column($filteredData, $param);
                    $results[$param] = [
                        'current' => $latestData ? $latestData[$param] : 0,
                        'max' => !empty($values) ? max($values) : 0,
                        'min' => !empty($values) ? min($values) : 0,
                        'average' => !empty($values) ? number_format(array_sum($values) / count($values), 2) : 0,
                    ];
                }
    
                return response()->json(['sensorData' => $results]);
            } else {
                return response()->json(['error' => 'Gagal mengambil data dari server'], $response->status());
            }
        } else {
            return response()->json(['error' => 'Gagal mengambil data dari server'], $response->status());
        }
    }
    
}
