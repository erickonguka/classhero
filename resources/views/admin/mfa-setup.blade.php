@extends('layouts.admin')

@section('title', 'MFA Setup')

@section('content')
<div class="p-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Multi-Factor Authentication</h1>
                <p class="text-gray-600 dark:text-gray-400">Secure your admin account with 2FA</p>
            </div>

            @if(auth()->user()->mfa_enabled)
                <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">MFA is Active</h3>
                            <p class="text-green-700 dark:text-green-300">Your account is protected with multi-factor authentication.</p>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">QR Code</h3>
                        <div class="bg-white p-4 rounded-lg border text-center">
                            @php
                                $google2fa = new \PragmaRX\Google2FA\Google2FA();
                                $secretKey = decrypt(auth()->user()->two_factor_secret);
                                $qrCodeUrl = $google2fa->getQRCodeUrl('ClassHero', auth()->user()->email, $secretKey);
                            @endphp
                            <canvas id="qrcode" class="mx-auto mb-4"></canvas>
                            <p class="text-sm text-gray-600">Scan with Google Authenticator</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Manual Setup</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Secret Key:</p>
                            <code class="block bg-white dark:bg-gray-600 p-2 rounded font-mono text-sm break-all">{{ $secretKey }}</code>
                            <button onclick="copySecret()" class="mt-2 text-sm text-blue-600 hover:text-blue-800">Copy Secret</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Backup Codes</h3>
                    <p class="text-gray-600 dark:text-gray-400">Save these backup codes in a secure location. You can use them to access your account if you lose your authenticator device.</p>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-2 font-mono text-sm">
                            @if(auth()->user()->two_factor_recovery_codes)
                                @foreach(json_decode(decrypt(auth()->user()->two_factor_recovery_codes)) as $code)
                                    <div class="bg-white dark:bg-gray-600 p-2 rounded text-center">{{ $code }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <button onclick="regenerateBackupCodes()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium">
                        Regenerate Backup Codes
                    </button>
                </div>
            @else
                <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">MFA Not Enabled</h3>
                            <p class="text-yellow-700 dark:text-yellow-300">Your admin account is not protected with multi-factor authentication.</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Contact your system administrator to enable MFA for your account.</p>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Run this command to enable MFA:</p>
                        <code class="block mt-2 bg-gray-800 text-green-400 p-2 rounded font-mono text-sm">
                            php artisan admin:setup-mfa {{ auth()->user()->email }}
                        </code>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
@if(auth()->user()->mfa_enabled)
    // Generate QR Code
    const qrCodeUrl = '{{ $qrCodeUrl }}';
    const canvas = document.getElementById('qrcode');
    if (qrCodeUrl && canvas) {
        QRCode.toCanvas(canvas, qrCodeUrl, { width: 200, margin: 2 }, function (error) {
            if (error) {
                console.error('QR Code generation error:', error);
                canvas.parentElement.innerHTML = '<p class="text-red-500">Error generating QR code</p>';
            }
        });
    }
@endif

function copySecret() {
    const secret = '{{ $secretKey ?? "" }}';
    navigator.clipboard.writeText(secret).then(() => {
        toastr.success('Secret key copied to clipboard!');
    });
}

function regenerateBackupCodes() {
    Swal.fire({
        title: 'Regenerate Backup Codes?',
        text: 'This will invalidate your current backup codes and generate new ones.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, regenerate!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Generated!', 'New backup codes have been generated.', 'success');
        }
    });
}
</script>
@endsection