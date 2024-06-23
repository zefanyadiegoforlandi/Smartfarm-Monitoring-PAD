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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DaftarSensorController extends Controller
{
    public function daftar_sensor(Request $request)
    {
        try {
            $token = session('jwt');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->get("http://localhost/smartfarm_jwt/");

            if ($response->successful()) {
                $apiData = $response->json();
                $lahan = collect(json_decode(json_encode($apiData['lahan']), false));
                $sensors = collect(json_decode(json_encode($apiData['sensor']), false))
                    ->sortByDesc('id_sensor');

                $sensor = $sensors->map(function ($sensor) use ($lahan) {
                    $matchedLahan = $lahan->firstWhere('id_lahan', $sensor->id_lahan);
                    $sensor->alamat_lahan = $matchedLahan ? $matchedLahan->alamat_lahan : 'Tidak ada lahan';
                    return $sensor;
                });

                // Ambil parameter pencarian dari request
                $search = $request->input('search');

                // Filter sensor berdasarkan kriteria pencarian
                if (!empty($search)) {
                    $sensor = $sensor->filter(function ($sensor) use ($search) {
                        return stripos($sensor->id_sensor, $search) !== false || 
                               stripos($sensor->id_lahan, $search) !== false ||
                               stripos($sensor->alamat_lahan, $search) !== false;
                    });
                }

                $perPage = 5;
                $currentPage = $request->input('page', 1);
                $paginator = new LengthAwarePaginator(
                    $sensor->forPage($currentPage, $perPage),
                    $sensor->count(),
                    $perPage,
                    $currentPage
                );
                $paginator->setPath(request()->url());

                return view('pages/add/daftar-sensor', compact('paginator', 'lahan', 'search'));
            } else {
                return redirect()->back()->withErrors('Gagal mengambil data sensor dari server.');
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function search_sensor(Request $request) {
        try {
            $token = session('jwt');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->get("http://localhost/smartfarm_jwt/");

            if ($response->successful()) {
                $apiData = $response->json();
                $lahan = collect(json_decode(json_encode($apiData['lahan']), false));
                $sensors = collect(json_decode(json_encode($apiData['sensor']), false))
                    ->sortByDesc('id_sensor'); 

                $sensor = $sensors->map(function ($sensor) use ($lahan) {
                    $matchedLahan = $lahan->firstWhere('id_lahan', $sensor->id_lahan);
                    $sensor->alamat_lahan = $matchedLahan ? $matchedLahan->alamat_lahan : 'Tidak ada lahan';
                    return $sensor;
                });

                $search = $request->search;

                // Filter sensor based on search criteria
                if (!empty($search)) {
                    $sensor = $sensor->filter(function ($sensor) use ($search) {
                        return stripos($sensor->id_sensor, $search) !== false || 
                               stripos($sensor->id_lahan, $search) !== false ||
                               stripos($sensor->alamat_lahan, $search) !== false;
                    });
                }

                return view('pages/search/search-sensor', compact('search', 'sensor', 'lahan'));
            } else {
                throw new \Exception('Gagal mengambil data sensor dari server.');
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function store_sensor(Request $request)
    {
        try {
            $token = session('jwt');
            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $request->validate([
                'id_lahan' => 'required',
                'tanggal_aktivasi' => 'required',
                'nama_sensor' => 'required',
            ], [
                'id_lahan.required' => 'Lahan wajib di isi',  
                'tanggal_aktivasi.required' => 'Tanggal aktivasi harus diisi.',
                'nama_sensor.required' => 'Nama sensor harus diisi.',
            ]);

            $url = "http://localhost/smartfarm_jwt/sensor/";
            $responseSensor = Http::withToken($token)->get($url);
            $sensorData = $responseSensor->json();

            $lastIdFromAPI = !empty($sensorData) ? end($sensorData)['id_sensor'] : null;

            // Menghitung ID berikutnya
            $nextNumber = $lastIdFromAPI ? intval(substr($lastIdFromAPI, 1)) + 1 : 1;
            $nextNumber = min($nextNumber, 1001);
            $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $newIdSensor = 'S' . $formattedNumber;

            $response = Http::withToken($token)->asForm()->post($url, [
                'id_sensor' => $newIdSensor,
                'nama_sensor' => $request->nama_sensor,
                'id_lahan' => $request->id_lahan,
                'tanggal_aktivasi' => $request->tanggal_aktivasi,
            ]);

            if ($response->failed()) {
                throw new \Exception('Gagal menyimpan data sensor: ' . $response->body());
            }

            return redirect('/pages/add/daftar-sensor')->with('tambah', 'Sensor berhasil ditambahkan');
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function read_sensor_edit($id_sensor)
    {
        try {
            $token = session('jwt');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->get("http://localhost/smartfarm_jwt/");

            if ($response->successful()) {
                $apiData = $response->json();
                $lahan = collect(json_decode(json_encode($apiData['lahan']), false));
                $sensors = collect(json_decode(json_encode($apiData['sensor']), false))
                    ->where('id_sensor', $id_sensor);

                $sensor = $sensors->map(function ($sensor) use ($lahan) {
                    $matchedLahan = $lahan->firstWhere('id_lahan', $sensor->id_lahan);
                    $sensor->alamat_lahan = $matchedLahan ? $matchedLahan->alamat_lahan : 'Tidak ada lahan';
                    return $sensor;
                });
                $sensor = $sensor->first();

                if (!$sensor) {
                    throw new \Exception('Sensor tidak ditemukan.');
                }

                return view('pages.edit-delete.read-sensor', compact('sensor'));
            } else {
                throw new \Exception('Gagal mengambil data sensor dari server.');
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function form_sensor_edit(string $id_sensor)
    {
        try {
            $token = session('jwt');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->get("http://localhost/smartfarm_jwt/");

            if ($response->successful()) {
                $apiData = $response->json();
                $lahan = collect(json_decode(json_encode($apiData['lahan']), false));
                $sensors = collect(json_decode(json_encode($apiData['sensor']), false))
                    ->where('id_sensor', $id_sensor);

                $sensor = $sensors->map(function ($sensor) use ($lahan) {
                    $matchedLahan = $lahan->firstWhere('id_lahan', $sensor->id_lahan);
                    $sensor->alamat_lahan = $matchedLahan ? $matchedLahan->alamat_lahan : 'Tidak ada lahan';
                    return $sensor;
                });
                $sensor = $sensor->firstWhere('id_sensor', $id_sensor);

                if (!$sensor) {
                    throw new \Exception('Sensor tidak ditemukan.');
                }

                return view('pages.edit-delete.form-sensor', compact('sensor', 'lahan'));
            } else {
                throw new \Exception('Gagal mengambil data sensor dari server.');
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function form_sensor_update(Request $request, $id_sensor)
    {
        try {
            $token = session('jwt');
            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            // Tidak perlu request data user dan lahan secara khusus kecuali dibutuhkan di view
            $request->validate([
                'id_lahan' => 'required',
                'nama_sensor' => 'required',
                'tanggal_aktivasi' => 'required',
            ], [
                'id_lahan.required' => 'Lahan wajib di isi',
                'nama_sensor.required' => 'Nama sensor wajib di isi',
                'tanggal_aktivasi.required' => 'Tanggal aktivasi harus diisi.',
            ]);

            $url = "http://localhost/smartfarm_jwt/sensor/{$id_sensor}";

            // Menggunakan Http facade untuk konsistensi dan kejelasan
            $response = Http::withToken($token)->asForm()->post($url, [
                'id_lahan' => $request->id_lahan,
                'nama_sensor' => $request->nama_sensor,
                'tanggal_aktivasi' => $request->tanggal_aktivasi,
            ]);

            if ($response->failed()) {
                throw new \Exception('Gagal memperbarui data sensor: ' . $response->body());
            }

            return redirect('/pages/add/daftar-sensor')->with('tambah', 'Sensor berhasil diperbarui');
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function read_sensor_destroy($id_sensor)
    {
        try {
            $token = session('jwt');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->delete("http://localhost/smartfarm_jwt/sensor/$id_sensor");

            if ($response->successful()) {
                return redirect('/pages/add/daftar-sensor')->with('delete', 'Sensor berhasil dihapus');
            } else {
                throw new \Exception('Gagal menghapus Sensor');
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
