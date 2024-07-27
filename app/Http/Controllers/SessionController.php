<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SessionController extends Controller
{
    public function setLahan(Request $request)
    {
        $request->validate([
            'id_lahan' => 'required',
        ]);

        session(['id_lahan' => $request->id_lahan]);

        $token = session('jwt');
        $id_lahan = $request->id_lahan;

        $sensorResponse = Http::withToken($token)->get(env('SENSOR_URL'));

        if ($sensorResponse->successful()) {
            $sensors = $sensorResponse->json();
            // Memfilter sensor berdasarkan id_lahan dan mengambil sensor pertama
            $filteredSensors = array_filter($sensors, function ($sensor) use ($id_lahan) {
                return $sensor['id_lahan'] == $id_lahan;
            });

            if (!empty($filteredSensors)) {
                $firstSensor = reset($filteredSensors);
                session(['id_sensor' => $firstSensor['id_sensor']]);
            } else {
                // Mengosongkan id_sensor pada session jika tidak ada sensor yang ditemukan
                session()->forget('id_sensor');
            }
        } else {
            return response()->json(['error' => 'Failed to retrieve sensor data'], $sensorResponse->status());
        }

        // Mengembalikan respon JSON dengan pesan sukses
        return response()->json(['message' => 'Session changed successfully']);
    }

    public function setSensor(Request $request)
    {
        $request->validate([
            'id_sensor' => 'required',
        ]);

        session(['id_sensor' => $request->id_sensor]);
        // Mengembalikan respon JSON dengan pesan sukses
        return response()->json(['message' => 'Sensor changed successfully']);
    }

    public function setTimeFrame(Request $request)
    {
        $request->validate([
            'timeFrame' => 'required|string',
        ]);

        session(['timeFrame' => $request->input('timeFrame')]);
        // Mengembalikan respon JSON dengan pesan sukses

        return response()->json(['message' => 'Time Frame added successfully']);
    }
}