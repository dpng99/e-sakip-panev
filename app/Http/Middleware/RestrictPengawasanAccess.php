<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictPengawasanAccess
{
    /**
     * Handle an incoming request.
     * Mencegah akun Pengawasan (888882 & wasxxxxxx) mengakses fitur operasional satker (Perencanaan, Pengukuran, Pelaporan, SAKIP Wilayah, Monitoring).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idSatker = (string) session('id_satker');
        $nama = strtolower(session('satkernama') ?? '');

        // Identifikasi Akun Pengawasan (888882, Pengawasan, Panev, dan wasxxxxxx)
        // Catatan: 419346 adalah satker operasional JAMWAS yang BUKAN akun penilai/pengawas.
        $isPengawasan = in_array($idSatker, ['888882', 'Pengawasan', 'Panev'])
            || str_starts_with(strtolower($idSatker), 'was')
            || ($nama === 'pengawasan' && $idSatker !== '419346');

        if ($isPengawasan) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: User Pengawasan hanya memiliki wewenang pada modul Evaluasi LKE dan Dokumen LHE AKIP.',
                ], 403);
            }

            return redirect()->route('evaluasi')
                ->with('error-lhe', 'Akses Dibatasi: Akun Pengawasan hanya berwenang mengakses modul Evaluasi LKE dan Dokumen LHE AKIP.')
                ->with('error', 'Akses Dibatasi: Akun Pengawasan hanya berwenang mengakses modul Evaluasi LKE dan Dokumen LHE AKIP.');
        }

        return $next($request);
    }
}
