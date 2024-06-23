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


class InformationUserController extends Controller
{
    public function read_user_information() {
        $token = session('jwt');
        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
        }
    
        // Ambil ID pengguna dari sesi, asumsikan 'user_id' disimpan dalam sesi saat pengguna login
        $id = session('user_id');
        if (!$id) {
            return redirect('/')->withErrors('ID Pengguna tidak ditemukan. Silakan login terlebih dahulu.');
        }
    
        $response = Http::withToken($token)->get("http://localhost/smartfarm_jwt/users/$id");
    
        if ($response->successful()) {
            $user = $response->json();
    
            if (!empty($user)) {
                return view('pages.edit-delete.read-user', compact('user'));
            } else {
                return redirect()->back()->withErrors('User tidak ditemukan.');
            }
        } else {
            return redirect()->back()->withErrors('Gagal mengambil data dari API.');
        }
    }
    
    
}