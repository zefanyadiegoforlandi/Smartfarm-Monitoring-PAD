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
    
            $response = Http::withToken($token)->get(env('API_BASE_URL'));
    
            if ($response->successful()) {
                $apiData = $response->json();
                $lahan = collect(json_decode(json_encode($apiData['lahan']), false));
                $sensors = collect(json_decode(json_encode($apiData['sensor']), false))
                    ->sortByDesc('id_sensor');
    
                $sensor = $sensors->map(function ($sensor) use ($lahan) {
                    $matchedLahan = $lahan->firstWhere('id_lahan', $sensor->id_lahan);
                    $sensor->nama_lahan = $matchedLahan ? $matchedLahan->nama_lahan : 'Tidak ada lahan';
                    $sensor->alamat_lahan = $matchedLahan ? $matchedLahan->alamat_lahan : 'Tidak ada lahan';
                    return $sensor;
                });
    
                // Ambil parameter pencarian dari request
                $search = $request->input('search');
    
                // Filter sensor berdasarkan kriteria pencarian
                if (!empty($search)) {
                    $sensor = $sensor->filter(function ($sensor) use ($search) {
                        return stripos($sensor->id_sensor, $search) !== false || 
                               stripos($sensor->nama_lahan, $search) !== false ||
                               stripos($sensor->nama_lahan, $search) !== false ||
                               stripos($sensor->alamat_lahan, $search) !== false ||
                               stripos($sensor->tanggal_aktivasi, $search) !== false;


                    });
                }
    
                $perPage = 10;
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
    
            $url = env('SENSOR_URL');
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
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }
    

    public function read_sensor_edit($id_sensor)
    {
        try {
            $token = session('jwt');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->get(env('API_BASE_URL'));

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

            $response = Http::withToken($token)->get(env('API_BASE_URL'));

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
    
            // Validasi request di Laravel
            $validated = $request->validate([
                'id_lahan' => 'required',
                'nama_sensor' => 'required',
                'tanggal_aktivasi' => 'required',
            ], [
                'id_lahan.required' => 'Lahan wajib diisi',
                'nama_sensor.required' => 'Nama sensor wajib diisi',
                'tanggal_aktivasi.required' => 'Tanggal aktivasi harus diisi.',
            ]);
    
            // Persiapan data untuk dikirim ke server eksternal
            $data = [
                'id_lahan' => $validated['id_lahan'],
                'nama_sensor' => $validated['nama_sensor'],
                'tanggal_aktivasi' => $validated['tanggal_aktivasi'],
            ];
    
            $url = env('SENSOR_URL') . $id_sensor;
    
            // Mengirim data ke server eksternal untuk pembaruan
            $response = Http::withToken($token)->asForm()->post($url, $data);
    
            if ($response->failed()) {
                // Mengambil pesan kesalahan dari server eksternal
                $errors = $response->json('errors');
                return redirect()->back()->withErrors($errors);
            }
    
            return redirect('/pages/add/daftar-sensor')->with('tambah', 'Sensor berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }
    

    
    
    
    public function read_sensor_destroy($id_sensor)
    {
        try {
            $token = session('jwt');
    
            if (!$token) {
                return back()->withErrors('Token tidak ditemukan. Silakan login terlebih dahulu.');
            }
    
            // Cek apakah id_sensor digunakan dalam tabel data_sensor
            $responseCheck = Http::withToken($token)->get(env('DATA_SENSOR_URL') . '?id_sensor=' . $id_sensor);
    
            if ($responseCheck->successful()) {
                $dataDataSensor = $responseCheck->json();
    
                // Periksa apakah ada data sensor yang terkait dengan id_sensor yang sedang dihapus
                $relatedDataSensor = array_filter($dataDataSensor, function($data_sensor) use ($id_sensor) {
                    return $data_sensor['id_sensor'] == $id_sensor;
                });
    
                if (!empty($relatedDataSensor)) {
                    return back()->with('error', 'Gagal menghapus sensor. Sensor memiliki data yang terkait pada data sensor.');
                }
            } else {
                throw new \Exception('Gagal memeriksa data sensor');
            }
    
            // Hapus sensor
            $responseDelete = Http::withToken($token)->delete(env('SENSOR_URL') . $id_sensor);
    
            if ($responseDelete->successful()) {
                return redirect('/pages/add/daftar-sensor')->with('tambah', 'Sensor berhasil dihapus');
            } else {
                throw new \Exception('Gagal menghapus sensor');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus sensor.');
        }
    }
    

    
}
