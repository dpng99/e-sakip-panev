<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class UbahpasswordController extends Controller
{
    public function index()
    {
        // Cek apakah tahun sudah dipilih
        if (!session()->has('tahun_terpilih')) {
            return redirect()->route('pilih.tahun');
        }

        $tahun = session('tahun_terpilih');
        return view('ubahpassword', ['tahun' => $tahun]);
    }

    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    $user = auth()->user();

    // Verifikasi MD5 tanpa dd() agar hash tidak bocor ke layar
    if (md5($request->current_password) !== $user->satkerpass) {
        return back()->withErrors(['current_password' => 'Password lama tidak cocok!']);
    }

    // Tetap simpan format MD5 agar muat di VARCHAR(32) sinori_login
    $user->update([
        'satkerpass' => md5($request->new_password),
    ]);

    return back()->with('success', 'Password berhasil diperbarui!');
}
}
