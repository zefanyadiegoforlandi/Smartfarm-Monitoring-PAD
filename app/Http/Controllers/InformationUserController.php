<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;

class InformationUserController extends Controller
{
    public function read_user_information(Request $request)
    {
        try {
            $token = session('jwt');
            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $id = session('user_id');
            if (!$id) {
                return redirect('/')->withErrors('ID Pengguna tidak ditemukan. Silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->get(env('API_BASE_URL'));
             
            if ($response->successful()) {
                $apiData = $response->json();
                $users = collect($apiData['users']);
                $lahan = collect($apiData['lahan']);
                $sensor = collect($apiData['sensor']);
         
                $user = $users->firstWhere('id', $id); 
         
                if ($user) {
                    $userLahan = $lahan->where('id_user', $user['id']);
                    $userSensors = $sensor->whereIn('id_lahan', $userLahan->pluck('id_lahan'))->unique('id_sensor');

                    // Gabungkan data sensor dan lahan untuk pagination
                    $sensorData = $userSensors->map(function ($sensor) use ($userLahan) {
                        $lahan = $userLahan->firstWhere('id_lahan', $sensor['id_lahan']);
                        return [
                            'nama_lahan' => $lahan['nama_lahan'] ?? 'Unknown',
                            'id_lahan' => $sensor['id_lahan'],
                            'nama_sensor' => $sensor['nama_sensor'] ?? 'No Sensor',
                            'id_sensor' => $sensor['id_sensor']
                        ];
                    });
         
                    $perPage = 5; 
                    $currentPage = $request->input('page', 1); 
                    $paginator = new LengthAwarePaginator(
                        $sensorData->forPage($currentPage, $perPage), 
                        $sensorData->count(), 
                        $perPage, 
                        $currentPage 
                    );
                    $paginator->setPath($request->url());
         
                    return view('pages.edit-delete.read-user', compact('user', 'paginator'));
                } else {
                    return redirect()->back()->withErrors('User tidak ditemukan.');
                }
            } else {
                throw new \Exception('Gagal mengambil data dari API.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
