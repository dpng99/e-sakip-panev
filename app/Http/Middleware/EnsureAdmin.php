<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     * Memastikan hanya Administrator Pusat / Pengawas yang dapat mengakses rute master data & operasional sistem.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $level = session('id_sakip_level');
        $idSatker = session('id_satker');

        $isAdmin = ($level == 99) 
            || in_array($idSatker, [999999, '999999', 'admin', 'Pengawasan', 'Panev', 'menpanrb']);

        if (!$isAdmin) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Fitur ini hanya dapat diakses oleh Administrator Pusat.',
                ], 403);
            }
            abort(403, 'Akses Ditolak: Fitur ini hanya dapat diakses oleh Administrator Pusat.');
        }

        return $next($request);
    }
}
