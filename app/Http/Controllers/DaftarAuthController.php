<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use GuzzleHttp\Client;

class DaftarAuthController extends Controller
{
    public function form_auth_edit() {
        $token = session('jwt');
        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
        }
    
        // Ambil ID pengguna dari sesi
        $id = session('user_id');
        if (!$id) {
            return redirect('/')->withErrors('ID Pengguna tidak ditemukan. Silakan login terlebih dahulu.');
        }
    
        $response = Http::withToken($token)->get(env('USERS_URL') . $id);
    
        if ($response->successful()) {
            $user = $response->json();
    
            // Check if the user data exists in the response
            if (!empty($user)) {
                return view('pages.edit-delete.form-auth', compact('user'));
            } else {
                return redirect()->back()->withErrors('User tidak ditemukan.');
            }
        } else {
            return redirect()->back()->withErrors('Gagal mengambil data dari API.');
        }
    }

    public function form_auth_update(Request $request, $id) {
        $token = session('jwt');
        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
        }
    
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
                    // Update session if name is updated
                    if (isset($updateData['name'])) {
                        session(['user_name' => $updateData['name']]);
                    }
    
                    // Cek hasil akhir yang disimpan
                    $updatedUserResponse = Http::withToken($token)->get(env('USERS_URL') . $id);
                    $updatedUser = $updatedUserResponse->json();
    
                    return redirect('/pages/add/daftar-farmer')->with('tambah', 'Data berhasil diupdate.');
                } else {
                    return redirect()->back()->with('error', 'Gagal menyimpan data petani');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal menyimpan data petani: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Gagal memeriksa database eksternal.');
        }
    }

    public function read_auth_edit() {
        $token = session('jwt');
        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
        }
    
        // Ambil ID pengguna dari sesi, asumsikan 'user_id' disimpan dalam sesi saat pengguna login
        $id = session('user_id');
        if (!$id) {
            return redirect('/')->withErrors('ID Pengguna tidak ditemukan. Silakan login terlebih dahulu.');
        }
    
        $response = Http::withToken($token)->get(env('USERS_URL') . $id);
    
        if ($response->successful()) {
            $user = $response->json();
    
            if (!empty($user)) {
                return view('pages.edit-delete.read-auth', compact('user'));
            } else {
                return redirect()->back()->withErrors('User tidak ditemukan.');
            }
        } else {
            return redirect()->back()->withErrors('Gagal mengambil data dari API.');
        }
    }
}
