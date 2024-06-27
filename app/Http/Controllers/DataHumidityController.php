<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;

class DataHumidityController extends Controller
{
    public function getData_Humidity()
    {
        try {
            $id_sensor = session('id_sensor');
            $id_lahan = session('id_lahan');
            $token = session('jwt'); // Menggunakan token untuk permintaan yang memerlukannya

            if (!$id_sensor) {
                return redirect('/')->withErrors('ID Sensor tidak ditemukan. Silakan login ulang.');
            }

            if (!$id_lahan) {
                return redirect('/')->withErrors('ID Lahan tidak ditemukan. Silakan login ulang.');
            }

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Silakan login ulang.');
            }

            // Mendapatkan semua data sensor
            $sensorResponse = Http::withToken($token)->get(env('SENSOR_URL'));

            if (!$sensorResponse->successful()) {
                return response()->json(['error' => 'Failed to retrieve sensor data'], $sensorResponse->status());
            }
            $sensorData = $sensorResponse->json();

            // Memfilter sensor berdasarkan id_lahan
            $sensors = array_filter($sensorData, function ($sensor) use ($id_lahan) {
                return $sensor['id_lahan'] == $id_lahan;
            });

            // Mendapatkan data sensor tertentu
            $response = Http::get(env('DATA_SENSOR_URL') . $id_sensor);

            if ($response->successful()) {
                $dataSensors = $response->json();

                $sortedData = collect($dataSensors)->slice(-20)->values();
                $dataSensor = $sortedData->map(function ($item) {
                    return [
                        'H' => $item['H'],
                        'TimeAdded' => $item['TimeAdded'],
                        'id_sensor' => $item['id_sensor']
                    ];
                })->toArray();

                $dataTabel = $sortedData->slice(-10)->values()->reverse()->map(function ($item) {
                    return [
                        'H' => $item['H'],
                        'TimeAdded' => $item['TimeAdded'],
                        'id_sensor' => $item['id_sensor']
                    ];
                })->toArray();

                $perPage = 5; 
                $currentPage = request()->input('page', 1); 

                // Membuat paginator dari koleksi yang dibalik
                $lengthAwarePaginator = new LengthAwarePaginator(
                    collect(array_reverse($dataSensor))->forPage($currentPage, $perPage), 
                    count($dataSensor), 
                    $perPage, 
                    $currentPage 
                );
                $lengthAwarePaginator->setPath(request()->url());
                $paginator = $lengthAwarePaginator->toArray();

                return view('/pages/data-sensor/humidity', compact('paginator', 'dataSensor', 'dataTabel', 'sensors'));
            } else {
                return response()->json(['error' => 'Failed to retrieve data sensors'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateDataGrafik_Humidity()
    {
        try {
            $id_sensor = session('id_sensor');

            if (!$id_sensor) {
                return redirect('/')->withErrors('ID Sensor tidak ditemukan. Silakan login ulang.');
            }

            $response = Http::get(env('DATA_SENSOR_URL') . $id_sensor);

            if ($response->successful()) {
                $dataSensors = $response->json();

                $sortedData = collect($dataSensors)->slice(-20)->values();

                $Humidity = $sortedData->pluck('H')->toArray();
                $TimeAdded = $sortedData->pluck('TimeAdded')->toArray();

                return response()->json(['Humidity' => $Humidity, 'TimeAdded' => $TimeAdded]);
            } else {
                return response()->json(['error' => 'Failed to retrieve data sensors'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateDataTable_Humidity()
    {
        try {
            $id_sensor = session('id_sensor');

            if (!$id_sensor) {
                return redirect('/')->withErrors('ID Sensor tidak ditemukan. Silakan login ulang.');
            }

            $response = Http::get(env('DATA_SENSOR_URL') . $id_sensor);

            if ($response->successful()) {
                $dataSensors = $response->json();

                $sortedData = collect($dataSensors)->slice(-10)->values();

                $dataTabel = $sortedData->map(function ($item) {
                    return [
                        'H' => $item['H'],
                        'TimeAdded' => $item['TimeAdded'],
                        'id_sensor' => $item['id_sensor']
                    ];
                })->toArray();

                return response()->json(['dataSensor' => $dataTabel]);
            } else {
                return response()->json(['error' => 'Failed to retrieve data sensors'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
