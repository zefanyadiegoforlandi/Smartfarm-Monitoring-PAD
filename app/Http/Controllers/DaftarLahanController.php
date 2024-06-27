<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use GuzzleHttp\Client;
use App\Rules\MaxWordsRule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DaftarLahanController extends Controller
{
    public function daftar_lahan(Request $request)
    {
        try {
            $token = session('jwt');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->get(env('API_BASE_URL'));

            if ($response->successful()) {
                $apiData = $response->json();
                $users = collect(json_decode(json_encode($apiData['users']), false));
                $lahan = collect(json_decode(json_encode($apiData['lahan']), false))
                    ->sortByDesc('id_lahan');

                $lahan = $lahan->map(function ($item) use ($users) {
                    $user = $users->where('id', $item->id_user)->first();
                    $item->user_name = $user ? $user->name : '';
                    return $item;
                });

                // Ambil parameter pencarian dari request
                $search = $request->input('search');

                // Filter lahan berdasarkan kriteria pencarian
                if (!empty($search)) {
                    $lahan = $lahan->filter(function ($lahan) use ($search) {
                        return stripos($lahan->id_lahan, $search) !== false || 
                            stripos($lahan->nama_lahan, $search) !== false ||
                            stripos($lahan->user_name, $search) !== false ||
                            stripos($lahan->alamat_lahan, $search) !== false;
                    });
                }

                $perPage = 5;
                $currentPage = $request->input('page', 1);
                $paginator = new LengthAwarePaginator(
                    $lahan->forPage($currentPage, $perPage),
                    $lahan->count(),
                    $perPage,
                    $currentPage
                );

                $paginator->setPath($request->url());

                return view('pages/add/daftar-lahan', compact('paginator', 'users', 'search'));
            } else {
                return redirect()->back()->withErrors('Gagal mengambil data lahan dari server.');
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function store_lahan(Request $request)
    {
        try {
            $token = session('jwt');
            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }
    
            $responseUsers = Http::withToken($token)->get(env('API_BASE_URL'));
            $apiData = $responseUsers->json();
    
            // Validasi request
            $request->validate([
                'id_user' => 'required',
                'nama_lahan' => ['required', 'string', 'max:20', new MaxWordsRule(20)],
                'alamat_lahan' => ['required', 'string', 'max:20', new MaxWordsRule(20)],
                'luas_lahan' => 'required|numeric|min:0',
            ], [
                'id_user.required' => 'Nama farmer wajib diisi',
                'id_user.exists' => 'nama farmer tidak tersedia dalam database.',
                'alamat_lahan.required' => 'Alamat lahan harus diisi.',
                'luas_lahan.required' => 'Luas lahan harus diisi.',
                'luas_lahan.numeric' => 'Luas lahan harus berupa nilai numerik.',
                'luas_lahan.min' => 'Luas lahan tidak boleh minus.',
            ]);
    
            $url = env('LAHAN_URL');
            $responseLahan = Http::withToken($token)->get($url);
            $lahanData = $responseLahan->json();
    
            $lastIdFromAPI = !empty($lahanData) ? end($lahanData)['id_lahan'] : null;
            $nextNumber = $lastIdFromAPI ? intval(substr($lastIdFromAPI, 1)) + 1 : 1;
            $nextNumber = min($nextNumber, 1001);
            $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $newIdLahan = 'L' . $formattedNumber;
    
            $response = Http::withToken($token)->asForm()->post($url, [
                'id_lahan' => $newIdLahan,
                'nama_lahan' => $request->nama_lahan,
                'id_user' => $request->id_user,
                'alamat_lahan' => $request->alamat_lahan,
                'luas_lahan' => $request->luas_lahan,
            ]);
    
            if ($response->failed()) {
                throw new \Exception($response->body());
            }
    
            return redirect('/pages/add/daftar-lahan')->with('tambah', 'Lahan berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }
    

    public function read_lahan_edit(Request $request, $id_lahan)
    {
        try {
            $token = session('jwt');
            if (!$token) {
                return redirect('/login')->withErrors('Silakan login untuk melanjutkan.');
            }

            $response = Http::withToken($token)->get(env('API_BASE_URL'));

            if ($response->successful()) {
                $apiData = $response->json();
                $users = collect(json_decode(json_encode($apiData['users']), false));
                $lahans = collect(json_decode(json_encode($apiData['lahan']), false))
                            ->where('id_lahan', $id_lahan); 

                $lahans = $lahans->map(function ($item) use ($users) {
                    $user = $users->where('id', $item->id_user)->first(); 
                    $item->user_name = $user ? $user->name : 'User tidak dikenal';
                    return $item;
                });

                $lahan = $lahans->first(); 

                if (!$lahan) {
                    throw new \Exception('Lahan tidak ditemukan.');
                }

                return view('pages.edit-delete.read-lahan', compact('lahan'));
            } else {
                throw new \Exception('Gagal mengambil data dari server.');
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function form_lahan_edit(string $id_lahan)
    {
        try {
            $token = Session::get('jwt');

            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->get(env('API_BASE_URL'));

            if ($response->successful()) {
                $apiData = $response->json();
                $users = collect(json_decode(json_encode($apiData['users']), false));
                $lahans = collect(json_decode(json_encode($apiData['lahan']), false));

                $lahan = $lahans->map(function ($item) use ($users) {
                    $user = $users->where('id', $item->id_user)->first();
                    $item->user_name = $user ? $user->name : 'User tidak ditemukan';
                    return $item;
                })->firstWhere('id_lahan', $id_lahan);

                if (!$lahan) {
                    throw new \Exception('Lahan dengan ID tersebut tidak ditemukan.');
                }

                return view('pages.edit-delete.form-lahan', compact('lahan', 'users'));
            } else {
                throw new \Exception('Gagal mengambil data dari server.');
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function form_lahan_update(Request $request, $id_lahan)
    {
        try {
            $token = session('jwt');
            if (!$token) {
                return redirect('/login')->withErrors('Anda perlu login untuk mengakses halaman ini.');
            }

            $request->validate([
                'id_user' => 'required',
                'nama_lahan' => ['required', 'string', 'max:20', new MaxWordsRule(20)],
                'alamat_lahan' => ['required', 'string', 'max:20', new MaxWordsRule(20)],
                'luas_lahan' => 'required|numeric|min:0',
            ], [
                'id_user.required' => 'Nama farmer wajib diisi',
                'id_user.exists' => 'nama farmer tidak tersedia dalam database.',
                'alamat_lahan.required' => 'Alamat lahan harus diisi.',
                'luas_lahan.required' => 'Luas lahan harus diisi.',
                'luas_lahan.numeric' => 'Luas lahan harus berupa nilai numerik.',
                'luas_lahan.min' => 'Luas lahan tidak boleh minus.',
            ]);

            $url = env('LAHAN_URL') . $id_lahan;

            $response = Http::withToken($token)->asForm()->post($url, [
                'id_user' => $request->id_user,
                'nama_lahan' => $request->nama_lahan,
                'alamat_lahan' => $request->alamat_lahan,
                'luas_lahan' => $request->luas_lahan,
            ]);

            if ($response->failed()) {
                throw new \Exception('Gagal memperbarui data lahan: ' . $response->body());
            }

            return redirect('/pages/add/daftar-lahan')->with('tambah', 'Lahan berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }

    public function read_lahan_destroy($id_lahan)
    {
        try {
            $token = session('jwt');
            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            $response = Http::withToken($token)->delete(env('LAHAN_URL') . $id_lahan);

            if ($response->successful()) {
                return redirect('/pages/add/daftar-lahan')->with('delete', 'Lahan berhasil dihapus');
            } else {
                throw new \Exception('Gagal menghapus lahan');
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
