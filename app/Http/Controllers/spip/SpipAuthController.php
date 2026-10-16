<?php

namespace App\Http\Controllers\Spip;

use Illuminate\Http\Request;
use App\Models\SpipUser;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class SpipAuthController extends Controller
{
    public function index()
    {
        return view('spip/spip_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'jenis_penilaian' => 'required|in:pm,pk',
        ]);

        $user = \App\Models\Spip\SpipUser::where('username', $request->username)->first();

        if ($user) {
            // Tentukan field password mana yang akan dicek
            $passwordDiDatabase = ($request->jenis_penilaian == 'pm') ? $user->password_pm : $user->password_pk;

            // Gunakan Hash::check untuk memverifikasi password Bcrypt
            if (\Illuminate\Support\Facades\Hash::check($request->password, $passwordDiDatabase)) {

                $request->session()->regenerate();
                session([
                    'is_logged_in' => true,
                    'user_id' => $user->id,
                    'satker' => $user->satker,
                    'username' => $user->username,
                    'jenis_penilaian' => $request->jenis_penilaian
                ]);

                return redirect('/dashboard-spip');
            }
        }

        return back()->withErrors(['login_error' => 'Username, Password, atau Jenis Penilaian tidak sesuai.']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:6',
        ]);

        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login.spip')->withErrors(['login_error' => 'Sesi berakhir, silakan login kembali.']);
        }
        $user = \App\Models\Spip\SpipUser::find($userId);
        if (!$user) {
            return redirect()->route('login.spip')->withErrors(['login_error' => 'Akun pengguna tidak ditemukan.']);
        }

        // Cek apakah password lama sesuai (tergantung apakah PM atau PK)
        $passwordField = (session('jenis_penilaian') == 'pm') ? 'password_pm' : 'password_pk';

        if (!Hash::check($request->old_password, $user->$passwordField)) {
            return back()->withErrors(['old_password' => 'Password lama tidak sesuai']);
        }

        // Update password
        $user->$passwordField = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah!');
    }

    public function logout(Request $request)
{
    // Hapus session
    $request->session()->flush();

    // Pastikan ini mengarah ke /spip
    return redirect('/spip');
}
}
