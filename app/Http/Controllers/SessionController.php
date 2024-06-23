<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function setLahan(Request $request)
    {
        $request->validate([
            'id_lahan' => 'required',
        ]);

        // Mengatur nilai session untuk id_lahan
        session(['id_lahan' => $request->id_lahan]);

        // Mengembalikan respon JSON dengan pesan sukses
        return response()->json(['message' => 'Session updated successfully']);
    }
}




