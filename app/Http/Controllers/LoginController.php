<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = "sm4rtf4rm"; // Pastikan ini kunci yang tepat yang digunakan untuk sign token
        $postData = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $response = Http::asForm()->post('http://localhost/smartfarm_jwt/login/', $postData);

        if ($response->successful()) {
            $data = $response->json();
            $token = $data['token'];

            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));

                $level = $decoded->level;
                $id = $decoded->id;

                // Menggunakan token untuk mendapatkan informasi lebih lanjut tentang pengguna
                $userInfoResponse = Http::withToken($token)->get("http://localhost/smartfarm_jwt/");
                if ($userInfoResponse->successful()) {
                    $userData = $userInfoResponse->json();

                    // Debugging point: Tampilkan data pengguna

                    // Mencari pengguna sesuai ID di dalam tabel users
                    $user = collect($userData['users'])->firstWhere('id', $id);
                    $lahan = collect($userData['lahan'])->where('id_user', $id)->sortBy('created_at')->first();


                    if (!$user) {
                        throw new \Exception("User not found.");
                    }

                    $name = $user['name']; // Misalkan setiap pengguna memiliki 'name'
                    
                    // Menyimpan data yang diperlukan di sesi
                    session([
                        'jwt' => $token,
                        'user_level' => $level,
                        'user_id' => $id,
                        'user_name' => $name,
                        'id_lahan' => $lahan['id_lahan'],
                    ]);

                    // Debugging point: Tampilkan data session setelah disimpan

                    if ($level === 'admin') {
                        return redirect()->intended(route('admin-dashboard'));
                    } else {
                        return redirect()->intended(route('user-dashboard'));
                    }
                } else {
                    throw new \Exception("Unable to retrieve user information.");
                }

            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['login' => 'Login failed or invalid token.']);
            }
        } else {
            return back()->withInput()->withErrors(['login' => 'Login failed, check email and password.']);
        }
    }
}
