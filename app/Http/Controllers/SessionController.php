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

        // Mengatur nilai session untuk id_lahan
        session(['id_lahan' => $request->id_lahan]);

        // Mendapatkan data sensor yang terkait dengan id_lahan
        $token = session('jwt'); // Menggunakan token dari session jika diperlukan
        $id_lahan = $request->id_lahan;

        // Mendapatkan data sensor yang dimiliki oleh id_lahan
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
                return response()->json(['error' => 'No sensors found for the specified id_lahan'], 404);
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

        // Mengatur nilai session untuk id_sensor
        session(['id_sensor' => $request->id_sensor]);

        // Mengembalikan respon JSON dengan pesan sukses
        return response()->json(['message' => 'Sensor changed successfully']);
    }
}
