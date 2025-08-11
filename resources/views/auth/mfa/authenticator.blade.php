@extends('layouts.app')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-100 to-purple-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Two-Factor Authentication</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Enter the 6-digit code from your authenticator app</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8">
            @if(!auth()->user()->two_factor_confirmed_at)
                <!-- First time setup -->
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Setup Authenticator App</h3>
                    @php
                        $google2fa = new \PragmaRX\Google2FA\Google2FA();
                        $secretKey = decrypt(auth()->user()->two_factor_secret);
                        $qrCodeUrl = $google2fa->getQRCodeUrl('ClassHero', auth()->user()->email, $secretKey);
                    @endphp
                    <div class="bg-white p-4 rounded-lg border mb-4">
                        <div id="qrcode" class="flex justify-center mb-2"></div>
                        <p class="text-sm text-gray-600">Scan with Google Authenticator</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Or enter manually:</p>
                        <code class="block bg-white dark:bg-gray-600 p-2 rounded font-mono text-sm break-all">{{ $secretKey }}</code>
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('mfa.verify.authenticator') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Authentication Code
                    </label>
                    <input id="code" name="code" type="text" maxlength="6" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors text-center text-2xl font-mono"
                           placeholder="000000">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
                    Verify Code
                </button>
            </form>
        </div>
        
        @if(!auth()->user()->two_factor_confirmed_at)
        <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrCodeUrl = '{{ $qrCodeUrl }}';
            const qrContainer = document.getElementById('qrcode');
            
            if (qrCodeUrl && qrContainer) {
                new QRCode(qrContainer, {
                    text: qrCodeUrl,
                    width: 200,
                    height: 200,
                    colorDark: '#000000',
                    colorLight: '#ffffff'
                });
            }
        });
        </script>
        @endif
    </div>
</div>
@endsection