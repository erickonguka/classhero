<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class MfaController extends Controller
{
    public function showMfaForm()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            if (!$user->two_factor_secret) {
                $this->setupMfa($user);
            }
            return view('auth.mfa.authenticator');
        }
        
        return redirect()->route('dashboard');
    }
    
    private function setupMfa($user)
    {
        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();
        
        $user->update([
            'two_factor_secret' => encrypt($secretKey),
            'mfa_enabled' => true
        ]);
    }

    public function verifyAuthenticator(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = Auth::user();
        $google2fa = new Google2FA();
        $secretKey = decrypt($user->two_factor_secret);
        
        if ($google2fa->verifyKey($secretKey, $request->code)) {
            session(['2fa_verified' => true]);
            return redirect()->intended(route('admin.dashboard'));
        }
        
        return back()->withErrors(['code' => 'Invalid authentication code']);
    }
    
    public function regenerateRecoveryCodes(Request $request)
    {
        $user = Auth::user();
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        }
        
        $user->update(['two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes))]);
        return response()->json(['success' => true, 'codes' => $recoveryCodes]);
    }
    
    public function disableMfa(Request $request)
    {
        $request->validate(['password' => 'required|current_password']);
        $user = Auth::user();
        
        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'mfa_enabled' => false
        ]);
        
        return response()->json(['success' => true]);
    }
}