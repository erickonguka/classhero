@extends('layouts.app')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-100 to-purple-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Two-Factor Authentication</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Scan QR code and enter 6-digit code</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8">
            @php
                $google2fa = new \PragmaRX\Google2FA\Google2FA();
                $secretKey = decrypt(auth()->user()->two_factor_secret);
                $qrCodeUrl = $google2fa->getQRCodeUrl('ClassHero', auth()->user()->email, $secretKey);
            @endphp
            
            <div class="text-center mb-6">
                <div class="bg-white p-4 rounded-lg border mb-4">
                    <div id="qrcode" class="flex justify-center"></div>
                </div>
                <code class="text-sm bg-gray-100 p-2 rounded">{{ $secretKey }}</code>
            </div>
            
            <form method="POST" action="{{ route('mfa.verify.authenticator') }}">
                @csrf
                <div class="mb-4">
                    <input id="code" name="code" type="text" maxlength="6" required
                           class="w-full px-4 py-3 border rounded-lg text-center text-2xl font-mono"
                           placeholder="000000">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg">
                    Verify Code
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrCodeUrl = {!! json_encode($qrCodeUrl) !!};
    const qrContainer = document.getElementById('qrcode');
    
    if (qrCodeUrl && qrContainer) {
        const qr = qrcode(0, 'M');
        qr.addData(qrCodeUrl);
        qr.make();
        qrContainer.innerHTML = qr.createImgTag(4);
    }
});
</script>
    </div>
</div>
@endsection