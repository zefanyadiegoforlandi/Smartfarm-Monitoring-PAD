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
use App\Rules\MaxWordsRule;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DaftarFarmerController extends Controller
{
    //DAFTAR TABLE
    public function daftar_farmer(Request $request)
    {
        try {
            $token = session('jwt');
         
            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }
         
            $response = Http::withToken($token)->get(env('API_BASE_URL'));
         
            if ($response->successful()) {
                $apiData = $response->json();
                $users = collect(json_decode(json_encode($apiData['users']), false))
                    ->where('level', 'user')
                    ->sortByDesc('id');
             
                // Ambil parameter pencarian dari request
                $search = $request->input('search');
             
                // Filter users based on search criteria
                if (!empty($search)) {
                    $users = $users->filter(function ($user) use ($search) {
                        return stripos($user->id, $search) !== false || 
                               stripos($user->name, $search) !== false || 
                               stripos($user->email, $search) !== false;
                    });
                }
         
                $perPage = 5;
                $currentPage = $request->input('page', 1);
                $paginator = new LengthAwarePaginator(
                    $users->forPage($currentPage, $perPage),
                    $users->count(),
                    $perPage,
                    $currentPage
                );
                $paginator->setPath(request()->url());
                $lahan = collect(json_decode(json_encode($apiData['lahan']), false));
                $sensor = collect(json_decode(json_encode($apiData['sensor']), false));
         
                foreach ($users as $user) {
                    $userLahanIds = collect($lahan)->where('id_user', $user->id)->pluck('id_lahan');
                    $totalUniqueSensors = collect($sensor)->whereIn('id_lahan', $userLahanIds)->unique('id_sensor')->count();
                    $user->totalUniqueSensors = $totalUniqueSensors;
                }
         
                return view('pages/add/daftar-farmer', compact('paginator', 'sensor', 'search'));
            } else {
                throw new \Exception('Gagal mengambil data dari API.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
     
    public function store_farmer(Request $request)
    {
        // Validasi input dasar tanpa keunikan email
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20', new MaxWordsRule(20)],
            'email' => 'required|email',
            'password' => 'required|min:8',
            'alamat_user' => 'required'
        ], [
            'name.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Email harus valid!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal 8 karakter!',
            'alamat_user.required' => 'Alamat lahan wajib diisi',
        ]);

        try {
            $token = session('jwt');
            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }

            // Cek keunikan email via API menggunakan form-data
            $response = Http::withToken($token)->asMultipart()->post(env('CHECK_EMAIL_URL'), [
                [
                    'name' => 'email',
                    'contents' => $validated['email']
                ]
            ]);

            if (!$response->successful()) {
                return redirect()->back()->withErrors('Gagal memeriksa keunikan email.');
            }

            if ($response->json()['message'] == 'Email is already in use.') {
                return redirect()->back()->withErrors('Email telah digunakan oleh user lain!');
            }

            // Lanjutkan proses simpan data jika email unik
            $client = new \GuzzleHttp\Client();
            $url = env('USERS_URL');

            try {
                $response = $client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token
                    ],
                    'form_params' => [
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'password' => $request->input('password'),
                        'level' => 'user',
                        'alamat_user' => $request->input('alamat_user'),
                    ]
                ]);

                if ($response->getStatusCode() == 200) {
                    return redirect('/pages/add/daftar-farmer')->with('tambah', 'Data berhasil ditambahkan');
                } else {
                    return redirect()->back()->with('error', 'Gagal menyimpan data petani');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal menyimpan data petani: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function read_farmer_edit($id)
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
                $lahan = collect(json_decode(json_encode($apiData['lahan']), false));
                $sensor = collect(json_decode(json_encode($apiData['sensor']), false));
         
                $sensors = collect(); 
         
                $user = $users->firstWhere('id', $id); 
         
                if ($user) {
                    $userLahanIds = $lahan->where('id_user', $user->id)->pluck('id_lahan');
                    $userSensors = $sensor->whereIn('id_lahan', $userLahanIds)->unique('id_sensor');
                    $sensors = $sensors->merge($userSensors);
         
                    $perPage = 5; 
                    $currentPage = request()->input('page', 1); 
                    $paginator = new LengthAwarePaginator(
                        $sensors->forPage($currentPage, $perPage), 
                        $sensors->count(), 
                        $perPage, 
                        $currentPage 
                    );
                    $paginator->setPath(request()->url());
         
                    return view('pages.edit-delete.read-farmer', compact('users', 'user', 'sensors', 'paginator'));
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

    public function form_farmer_edit(string $id)
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
         
                $user = $users->firstWhere('id', $id);
         
                if ($user) {
                    return view('pages.edit-delete.form-farmer', compact('users', 'user'));
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

    public function form_farmer_update(Request $request, $id)
    {
        $token = session('jwt');
        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
        }

        try {
            // Ambil data pengguna dari API
            $response = Http::withToken($token)->get(env('USERS_URL') . $id);

            if ($response->successful()) {
                $user = $response->json();

                if (!$user) {
                    return redirect()->back()->with('error', 'User tidak ditemukan.');
                }

                // Validasi data yang diinput
                $validateData = $request->validate([
                    'name' => 'required',
                    'email' => [
                        'required',
                        'email',
                        function ($attribute, $value, $fail) use ($user, $token) {
                            if ($value !== $user['email']) {
                                // Memeriksa di database apakah email sudah ada
                                $existingUserResponse = Http::withToken($token)->get(env('USERS_URL') . '?email=' . $value);
                                $existingUsers = $existingUserResponse->json();

                                // Hanya melanjutkan jika ada pengguna yang ditemukan
                                if (!empty($existingUsers) && is_array($existingUsers)) {
                                    foreach ($existingUsers as $existingUser) {
                                        if ($existingUser['email'] === $value && $existingUser['id'] != $user['id']) {
                                            $fail('Email telah digunakan oleh user lain!');
                                            return;
                                        }
                                    }
                                }
                            }
                        },
                    ],
                    'password' => 'required|min:8',
                    'alamat_user' => 'required'
                ]);

                // Membuat array untuk menyimpan field yang perlu diupdate
                $updateData = [];
                if ($request->input('name') !== $user['name']) {
                    $updateData['name'] = $request->input('name');
                }
                if ($request->input('email') !== $user['email']) {
                    $updateData['email'] = $request->input('email');
                }
                if ($request->input('password') !== $user['password']) {
                    $updateData['password'] = $request->input('password');
                }
                if ($request->input('alamat_user') !== $user['alamat_user']) {
                    $updateData['alamat_user'] = $request->input('alamat_user');
                }

                // Menambahkan level jika tidak ada di request
                $updateData['level'] = 'user';

                if (empty($updateData)) {
                    return redirect()->back()->with('info', 'Tidak ada perubahan data.');
                }

                // Proses update jika validasi berhasil
                $client = new \GuzzleHttp\Client();
                $url = env('USERS_URL') . $id;

                try {
                    $response = $client->post($url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token
                        ],
                        'form_params' => $updateData
                    ]);

                    if ($response->getStatusCode() == 200) {
                        // Cek hasil akhir yang disimpan
                        $updatedUserResponse = Http::withToken($token)->get(env('USERS_URL') . $id);
                        $updatedUser = $updatedUserResponse->json();

                        return redirect('/pages/add/daftar-farmer')->with('tambah', 'Farmer berhasil diupdate.');
                    } else {
                        return redirect()->back()->with('error', 'Gagal menyimpan data petani');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Gagal menyimpan data petani: ' . $e->getMessage());
                }
            } else {
                return redirect()->back()->with('error', 'Gagal memeriksa database eksternal.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function read_farmer_destroy($id)
    {
        try {
            $token = session('jwt');
    
            if (!$token) {
                return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
            }
    
            // Cek apakah id_user digunakan dalam tabel lain (misalnya: lahan)
            $responseCheck = Http::withToken($token)->get(env('LAHAN_URL') . '?id_user=' . $id);
    
            if ($responseCheck->successful()) {
                $dataLahan = $responseCheck->json();
    
                // Logging untuk melihat respons dari API
                \Log::info('Response from LAHAN_URL:', ['data' => $dataLahan]);
    
                // Periksa apakah ada lahan yang terkait dengan id_user yang sedang dihapus
                $relatedLahan = array_filter($dataLahan, function($lahan) use ($id) {
                    return $lahan['id_user'] == $id;
                });
    
                if (!empty($relatedLahan)) {
                    return back()->with('error', 'Gagal menghapus farmer. Farmer memiliki data yang terkait pada lahan.');
                }
            } else {
                throw new \Exception('Gagal memeriksa data farmer');
            }
    
            // Hapus farmer
            $responseDelete = Http::withToken($token)->delete(env('USERS_URL') . $id);
    
            if ($responseDelete->successful()) {
                return redirect('/pages/add/daftar-farmer')->with('tambah', 'Farmer berhasil dihapus');
            } else {
                throw new \Exception('Gagal menghapus farmer');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
}
