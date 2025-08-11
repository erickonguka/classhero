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
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border text-center">
                            @php
                                $google2fa = new \PragmaRX\Google2FA\Google2FA();
                                $secretKey = decrypt(auth()->user()->two_factor_secret);
                                $qrCodeUrl = $google2fa->getQRCodeUrl('ClassHero', auth()->user()->email, $secretKey);
                            @endphp
                            <div id="qr-container" class="flex justify-center items-center mb-4" style="min-height: 200px;">
                                <canvas id="qrcode" width="200" height="200"></canvas>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Scan with Google Authenticator or similar app</p>
                            @if(config('app.debug'))
                                <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-600 rounded text-xs">
                                    <p><strong>Debug Info:</strong></p>
                                    <p>QR URL: <code class="text-xs break-all">{{ $qrCodeUrl }}</code></p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Manual Setup</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Secret Key:</p>
                            <div class="flex items-center justify-between bg-white dark:bg-gray-600 p-2 rounded">
                                <code id="secret-key" class="font-mono text-sm break-all flex-1">{{ $secretKey }}</code>
                                <button onclick="copySecret()" class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
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

                    <div class="flex space-x-4">
                        <button onclick="regenerateBackupCodes()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Regenerate Backup Codes
                        </button>
                        <button onclick="disableMfa()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Disable MFA
                        </button>
                    </div>
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
                    <p class="text-gray-600 dark:text-gray-400 mb-6">MFA will be automatically set up when you next log in.</p>
                    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm font-semibold text-blue-800 dark:text-blue-200">Automatic Setup</p>
                        </div>
                        <p class="text-sm text-blue-700 dark:text-blue-300">Your MFA will be configured automatically during your next login. You'll be guided through the setup process with QR code and backup codes.</p>
                    </div>
                    <div class="mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Or run this command manually:</p>
                        <code class="block bg-gray-800 text-green-400 p-2 rounded font-mono text-sm">
                            php artisan admin:setup-mfa {{ auth()->user()->email }}
                        </code>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js" onerror="loadQRCodeFallback()"></script>
<script>
function loadQRCodeFallback() {
    console.warn('Primary QRCode CDN failed, trying fallback');
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js';
    script.onerror = function() {
        console.error('All QRCode CDNs failed');
        const container = document.getElementById('qr-container');
        if (container) {
            container.innerHTML = '<div class="text-center"><p class="text-red-500 text-sm mb-2">QR Code library failed to load</p><p class="text-xs text-gray-500 dark:text-gray-400">Please use the manual setup key below</p></div>';
        }
    };
    document.head.appendChild(script);
}
</script>
<script>
@if(auth()->user()->mfa_enabled)
    document.addEventListener('DOMContentLoaded', function() {
        // Generate QR Code
        const qrCodeUrl = {!! json_encode($qrCodeUrl) !!};
        const canvas = document.getElementById('qrcode');
        const container = document.getElementById('qr-container');
        
        console.log('QR Code URL:', qrCodeUrl);
        console.log('Canvas element:', canvas);
        
        if (qrCodeUrl && canvas && typeof QRCode !== 'undefined') {
            try {
                QRCode.toCanvas(canvas, qrCodeUrl, { 
                    width: 200, 
                    height: 200,
                    margin: 2,
                    color: {
                        dark: '#000000',
                        light: '#ffffff'
                    }
                }, function (error) {
                    if (error) {
                        console.error('QR Code generation error:', error);
                        if (container) {
                            container.innerHTML = '<div class="text-center"><p class="text-red-500 text-sm mb-2">Error generating QR code</p><p class="text-xs text-gray-500 dark:text-gray-400">Please use the manual setup key below</p></div>';
                        }
                    } else {
                        console.log('QR Code generated successfully');
                    }
                });
            } catch (error) {
                console.error('QR Code library error:', error);
                if (container) {
                    container.innerHTML = '<div class="text-center"><p class="text-red-500 text-sm mb-2">QR Code library not loaded</p><p class="text-xs text-gray-500 dark:text-gray-400">Please use the manual setup key below</p></div>';
                }
            }
        } else {
            console.error('Missing QR code URL, canvas element, or QRCode library');
            if (container) {
                container.innerHTML = '<div class="text-center"><p class="text-red-500 text-sm mb-2">Unable to generate QR code</p><p class="text-xs text-gray-500 dark:text-gray-400">Please use the manual setup key below</p></div>';
            }
        }
        
        // Add a fallback check after a short delay
        setTimeout(function() {
            if (canvas && canvas.width === 0) {
                console.warn('QR code may not have generated properly, showing fallback');
                if (container) {
                    container.innerHTML = '<div class="text-center"><p class="text-yellow-600 text-sm mb-2">QR code generation delayed</p><p class="text-xs text-gray-500 dark:text-gray-400">Please use the manual setup key below or refresh the page</p></div>';
                }
            }
        }, 3000);
    });
@endif

function copySecret() {
    const secret = {!! json_encode($secretKey ?? '') !!};
    const button = event.target.closest('button');
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(secret).then(() => {
            // Show success feedback
            const originalContent = button.innerHTML;
            button.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
            
            if (typeof toastr !== 'undefined') {
                toastr.success('Secret key copied to clipboard!');
            }
            
            setTimeout(() => {
                button.innerHTML = originalContent;
            }, 2000);
        }).catch(err => {
            console.error('Could not copy text: ', err);
            fallbackCopy();
        });
    } else {
        fallbackCopy();
    }
    
    function fallbackCopy() {
        // Fallback: select the text
        const secretElement = document.getElementById('secret-key');
        if (secretElement) {
            const range = document.createRange();
            range.selectNode(secretElement);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            alert('Secret key selected. Press Ctrl+C to copy.');
        }
    }
}

function regenerateBackupCodes() {
    if (!confirm('This will invalidate your current backup codes and generate new ones. Are you sure?')) {
        return;
    }
    
    fetch('{{ route("admin.mfa.regenerate-codes") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the backup codes display
            const codesContainer = document.querySelector('.grid.grid-cols-2.gap-2');
            codesContainer.innerHTML = '';
            data.codes.forEach(code => {
                const codeDiv = document.createElement('div');
                codeDiv.className = 'bg-white dark:bg-gray-600 p-2 rounded text-center';
                codeDiv.textContent = code;
                codesContainer.appendChild(codeDiv);
            });
            alert('New backup codes have been generated successfully!');
        } else {
            alert('Error generating backup codes. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating backup codes. Please try again.');
    });
}

function disableMfa() {
    const password = prompt('Enter your password to disable MFA:');
    if (!password) return;
    
    if (!confirm('Are you sure you want to disable MFA? This will make your account less secure.')) {
        return;
    }
    
    fetch('{{ route("admin.mfa.disable") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ password: password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('MFA has been disabled successfully!');
            location.reload();
        } else {
            alert('Error disabling MFA. Please check your password and try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error disabling MFA. Please try again.');
    });
}
</script>
@endsection