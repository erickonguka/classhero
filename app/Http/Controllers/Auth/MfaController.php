<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;

class MfaController extends Controller
{
    public function showMfaForm()
    {
        $user = Auth::user();
        
        // Check if device is trusted
        if ($this->isDeviceTrusted($user)) {
            session(['mfa_verified' => true]);
            return redirect()->intended(route('dashboard'));
        }
        
        if ($user->isAdmin() && $user->mfa_enabled) {
            return view('auth.mfa.authenticator');
        } elseif (in_array($user->role, ['admin', 'teacher'])) {
            return view('auth.mfa.email-otp');
        }
        
        return redirect()->route('dashboard');
    }

    public function verifyAuthenticator(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();
        
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);
        
        if ($valid) {
            // Mark as confirmed on first successful verification
            if (!$user->two_factor_confirmed_at) {
                $user->update(['two_factor_confirmed_at' => now()]);
            }
            
            session(['mfa_verified' => true]);
            $this->trustDevice($user);
            return redirect()->intended(route('admin.dashboard'));
        }
        
        return back()->withErrors(['code' => 'Invalid authentication code']);
    }

    public function sendEmailOtp()
    {
        $user = Auth::user();
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        Mail::raw("Your ClassHero login code is: {$otp}\n\nThis code expires in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('ClassHero Login Code');
        });

        return response()->json(['success' => true, 'message' => 'OTP sent to your email']);
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        
        if ($user->otp_code === $request->otp && $user->otp_expires_at > now()) {
            $user->update(['otp_code' => null, 'otp_expires_at' => null]);
            session(['mfa_verified' => true]);
            $this->trustDevice($user);
            $route = $user->isAdmin() ? 'admin.dashboard' : 'teacher.dashboard';
            return redirect()->intended(route($route));
        }
        
        return back()->withErrors(['otp' => 'Invalid or expired OTP code']);
    }
    
    private function getDeviceFingerprint()
    {
        return hash('sha256', request()->userAgent() . request()->ip());
    }
    
    private function isDeviceTrusted($user)
    {
        $fingerprint = $this->getDeviceFingerprint();
        $trustedDevices = $user->trusted_devices ?? [];
        
        foreach ($trustedDevices as $device) {
            if ($device['fingerprint'] === $fingerprint && $device['expires_at'] > now()->timestamp) {
                return true;
            }
        }
        
        return false;
    }
    
    private function trustDevice($user)
    {
        $fingerprint = $this->getDeviceFingerprint();
        $trustedDevices = $user->trusted_devices ?? [];
        
        // Remove expired devices
        $trustedDevices = array_filter($trustedDevices, function($device) {
            return $device['expires_at'] > now()->timestamp;
        });
        
        // Add current device (trust for 30 days)
        $trustedDevices[] = [
            'fingerprint' => $fingerprint,
            'created_at' => now()->timestamp,
            'expires_at' => now()->addDays(30)->timestamp,
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip()
        ];
        
        $user->update(['trusted_devices' => array_values($trustedDevices)]);
    }
}