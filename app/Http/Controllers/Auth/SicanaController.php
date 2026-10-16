<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;

use App\Models\User;

use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;

use Illuminate\Support\Facades\Http;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class SicanaController extends Controller
{


    public function receiveToken(Request $request)
    {
        $tokenString = $request->query('token');

        if (!$tokenString) {
            return response()->json(['error' => 'No token provided'], 400);
        }

        $signer = new Sha256();
        $secretKey = config('services.sicana.secret');
        if (!$secretKey) {
            throw new \Exception('JWT Secret Key not configured');
        }
        $key = InMemory::plainText($secretKey);

        $config = Configuration::forSymmetricSigner($signer, $key);

        try {
            $token = $config->parser()->parse($tokenString);

            $constraints = [
                new \Lcobucci\JWT\Validation\Constraint\IssuedBy('sicana-auth-service'),
                new \Lcobucci\JWT\Validation\Constraint\PermittedFor('panev-app'),
                new \Lcobucci\JWT\Validation\Constraint\SignedWith($signer, $key),
            ];

            // Validasi expiry jika class LooseValidAt dan SystemClock tersedia
            if (class_exists(\Lcobucci\Clock\SystemClock::class) && class_exists(\Lcobucci\JWT\Validation\Constraint\LooseValidAt::class)) {
                $clock = new \Lcobucci\Clock\SystemClock(new \DateTimeZone('UTC'));
                $constraints[] = new \Lcobucci\JWT\Validation\Constraint\LooseValidAt($clock);
            }

            foreach ($constraints as $constraint) {
                if (!$config->validator()->validate($token, $constraint)) {
                    throw new \Exception('Invalid token constraints');
                }
            }

            $claims = $token->claims();

            $userData = [
                'id' => $claims->get('jti'),
                'username' => $claims->get('username'),
                'nama' => $claims->get('nama'),
                'satuan_kerja' => $claims->get('satuan_kerja'),
                'satker_nama' => $claims->get('satker_nama'),
                'id_kejati' => $claims->get('id_kejati'),
                'id_kejari' => $claims->get('id_kejari'),
                'id_sakip_level' => $claims->get('id_sakip_level'),
            ];

            $user = User::where('id_satker', $userData['satuan_kerja'])->first();

            if (!$user) {
    $user = User::create([
        'id_satker' => $userData['satuan_kerja'],
        'satkerpass' => md5(\Illuminate\Support\Str::random(32)), // String MD5 acak 32 karakter
        'id_kejati' => $userData['id_kejati'],
        'id_kejari' => $userData['id_kejari'],
        'id_sakip_level' => $userData['id_sakip_level'],
        'id_hidesatker' => 1,
        'satkernama' => $userData['satker_nama'],
    ]);
}

            Auth::login($user);

            $request->session()->put('id_satker', $user->id_satker);
            $request->session()->put('satkernama', str_replace('_', ' ', $user->satkernama));
            $request->session()->put('id_sakip_level', $user->id_sakip_level);

            // SET DEFAULT TAHUN SAAT LOGIN
            $request->session()->forget(['tahun_terpilih', 'tahun_ui', 'tw4_khusus']);

            $request->session()->put([
                'tahun_terpilih' => date('Y'),
                'tahun_ui'       => date('Y'),
                'tw4_khusus'     => 0, // false
            ]);
            
            return redirect('/dashboard');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
