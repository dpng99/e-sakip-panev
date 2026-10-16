<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth/login');
    }

    // Menangani login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // SET DEFAULT TAHUN SAAT LOGIN
        session()->forget(['tahun_terpilih', 'tahun_ui', 'tw4_khusus']);
        session([
            'tahun_terpilih' => date('Y'),
            'tahun_ui' => date('Y'),
            'tw4_khusus' => 0, // false = 0
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        // Fetch user from database
        $user = DB::table('sinori_login')->where('id_satker', $email)->first();

        if ($user && md5($password) === $user->satkerpass) {
    // Regenerasi ID sesi untuk mencegah Session Fixation
    $request->session()->regenerate();

    $request->session()->put('id_satker', $user->id_satker);
    $request->session()->put('satkernama', str_replace('_', ' ', $user->satkernama));
    $request->session()->put('id_sakip_level', $user->id_sakip_level);
    $request->session()->put('id_kejati', $user->id_kejati);

    auth()->loginUsingId($user->id_satker);
    return redirect()->route('dashboard');
}

        return back()->withErrors(['email' => 'User atau Password yang dimasukan salah!']);
    }

    // Menangani logout
    public function logout(Request $request)
    {
        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
