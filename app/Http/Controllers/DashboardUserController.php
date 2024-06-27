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
            $id_sensor = session('id_sensor');
            $id_lahan = session('id_lahan');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Silakan login terlebih dahulu.');
            }

            if (!$id_sensor) {
                return redirect('/')->withErrors('ID Sensor tidak ditemukan. Silakan login ulang.');
            }

            if (!$id_lahan) {
                return redirect('/')->withErrors('ID Lahan tidak ditemukan. Silakan login ulang.');
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

                $sensorResponse = Http::withToken($token)->get(env('SENSOR_URL'));
                if (!$sensorResponse->successful()) {
                    return response()->json(['error' => 'Failed to retrieve sensor data'], $sensorResponse->status());
                }
                $sensorData = $sensorResponse->json();

                $sensors = array_filter($sensorData, function ($sensor) use ($id_lahan) {
                    return $sensor['id_lahan'] == $id_lahan;
                });

                $response = Http::get(env('DATA_SENSOR_URL') . $id_sensor);
                if ($response->successful()) {
                    $dataSensors = $response->json();

                    $sortedData = collect($dataSensors)->slice(-20)->values();
                    $dataSensor = $sortedData->map(function ($item) {
                        return [
                            'Temperature' => $item['Temperature'],
                            'TimeAdded' => $item['TimeAdded'],
                            'id_sensor' => $item['id_sensor']
                        ];
                    })->toArray();

                    $dataTabel = $sortedData->slice(-10)->values()->reverse()->map(function ($item) {
                        return [
                            'Temperature' => $item['Temperature'],
                            'TimeAdded' => $item['TimeAdded'],
                            'id_sensor' => $item['id_sensor']
                        ];
                    })->toArray();

                    $perPage = 5; 
                    $currentPage = request()->input('page', 1); 

                    $lengthAwarePaginator = new LengthAwarePaginator(
                        collect(array_reverse($dataSensor))->forPage($currentPage, $perPage), 
                        count($dataSensor), 
                        $perPage, 
                        $currentPage 
                    );
                    $lengthAwarePaginator->setPath(request()->url());
                    $paginator = $lengthAwarePaginator->toArray();

                    // New addition to fetch and process the sensor data
                    $sensorDataResponse = Http::get(env('DATA_SENSOR_URL'));
                    if ($sensorDataResponse->successful()) {
                        $sensorDataAll = $sensorDataResponse->json();

                        $id_sensors = collect($sensors)->pluck('id_sensor');

                        $filteredData = collect($sensorDataAll)->filter(function ($data) use ($id_sensors) {
                            return in_array($data['id_sensor'], $id_sensors->toArray());
                        })->toArray();

                        $parameters = ['Light', 'PersentaseKelembapanTanah', 'AirQuality', 'RainDrop', 'H', 'Temperature', 'Pressure', 'ApproxAltitude'];
                        $results = [];

                        foreach ($parameters as $param) {
                            $values = array_column($filteredData, $param);
                            $results[$param] = [
                                'current' => end($values),
                                'max' => max($values),
                                'min' => min($values),
                                'average' => number_format(array_sum($values) / count($values), 2),
                            ];
                        }

                        return view('/pages/dashboard/user-dashboard', compact('paginator', 'nama_lahan', 'lahan', 'sensor', 'dataSensor', 'dataTabel', 'sensors', 'results'));
                    } else {
                        return response()->json(['error' => 'Failed to retrieve data sensors'], $sensorDataResponse->status());
                    }
                } else {
                    return response()->json(['error' => 'Failed to retrieve data sensors'], $response->status());
                }
            } else {
                throw new \Exception('Gagal mengambil data dari API.');
            }

        } catch (\Exception $e) {
            Log::error('Error in DashboardUserController@index: ' . $e->getMessage());
            return redirect('/')->withErrors('Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}
