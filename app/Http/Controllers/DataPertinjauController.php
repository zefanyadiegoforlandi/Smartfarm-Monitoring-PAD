<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DataPertinjauController extends Controller
{
    // Metode untuk menampilkan view utama dan mengirimkan data
    public function showPreview()
    {
        // Mendapatkan id_lahan dan token dari session
        $id_lahan = session('id_lahan');
        $token = session('jwt');

        $response = Http::withToken($token)->get('http://localhost/smartfarm_jwt/');

        if ($response->successful()) {
            $data = $response->json();

            // Mendapatkan sensor yang memiliki id_lahan yang sama dengan id_lahan dari session
            $sensors = collect($data['sensor'])->filter(function ($sensor) use ($id_lahan) {
                return $sensor['id_lahan'] == $id_lahan;
            });

            // Jika tidak ada sensor yang ditemukan, kembalikan view dengan pesan yang sesuai
            if ($sensors->isEmpty()) {
                return view('pertinjau', ['sensorData' => []]);
            }

            // Mengambil semua id_sensor dari sensor yang ditemukan
            $id_sensors = $sensors->pluck('id_sensor');

            // Mengambil data_sensor dari endpoint http://localhost/smartfarm_jwt/data_sensor/
            $sensorDataResponse = Http::get('http://localhost/smartfarm_jwt/data_sensor/');

            if ($sensorDataResponse->successful()) {
                $sensorData = $sensorDataResponse->json();

                // Filter data berdasarkan id_sensor
                $filteredData = collect($sensorData)->filter(function ($data) use ($id_sensors) {
                    return in_array($data['id_sensor'], $id_sensors->toArray());
                })->toArray();

                // Hitung nilai current, max, min, dan average untuk setiap parameter
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

                // Mengembalikan view dengan data
                return view('/user/pertinjau', ['sensorData' => $results]);
            } else {
                return response()->json(['error' => 'Gagal mengambil data dari server'], $response->status());
            }
        } else {
            return response()->json(['error' => 'Gagal mengambil data dari server'], $response->status());
        }
    }

    // Metode baru untuk menangani permintaan AJAX dan mengembalikan data sensor dalam format JSON
    public function getData()
    {
        // Mendapatkan id_lahan dan token dari session
        $id_lahan = session('id_lahan');
        $token = session('jwt');

        // Mendapatkan data dari endpoint http://localhost/smartfarm_jwt/
        $response = Http::withToken($token)->get('http://localhost/smartfarm_jwt/');

        if ($response->successful()) {
            $data = $response->json();

            // Mendapatkan sensor yang memiliki id_lahan yang sama dengan id_lahan dari session
            $sensors = collect($data['sensor'])->filter(function ($sensor) use ($id_lahan) {
                return $sensor['id_lahan'] == $id_lahan;
            });

            // Jika tidak ada sensor yang ditemukan, kembalikan respons yang sesuai
            if ($sensors->isEmpty()) {
                return response()->json(['sensorData' => []]);
            }

            // Mengambil semua id_sensor dari sensor yang ditemukan
            $id_sensors = $sensors->pluck('id_sensor');

            // Mengambil data_sensor dari endpoint http://localhost/smartfarm_jwt/data_sensor/
            $sensorDataResponse = Http::get('http://localhost/smartfarm_jwt/data_sensor/');

            if ($sensorDataResponse->successful()) {
                $sensorData = $sensorDataResponse->json();

                // Filter data berdasarkan id_sensor
                $filteredData = collect($sensorData)->filter(function ($data) use ($id_sensors) {
                    return in_array($data['id_sensor'], $id_sensors->toArray());
                })->toArray();

                // Hitung nilai current, max, min, dan average untuk setiap parameter
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

                // Mengembalikan data dalam format JSON
                return response()->json(['sensorData' => $results]);
            } else {
                return response()->json(['sensorData' => []]);
            }
        } else {
            return response()->json(['sensorData' => []]);
        }
    }
}
