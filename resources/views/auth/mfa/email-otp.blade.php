@extends('layouts.app')

@section('title', 'Email Verification')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-100 to-purple-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Email Verification</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">We'll send a 6-digit code to your email</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8">
            <div id="send-otp-section">
                <button onclick="sendOtp()" id="send-otp-btn"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
                    Send Verification Code
                </button>
            </div>

            <form method="POST" action="{{ route('mfa.verify.email') }}" id="verify-form" class="space-y-6 hidden">
                @csrf
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Verification Code
                    </label>
                    <input id="otp" name="otp" type="text" maxlength="6" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors text-center text-2xl font-mono"
                           placeholder="000000">
                    @error('otp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
                    Verify Code
                </button>
                <button type="button" onclick="sendOtp()" 
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    Resend Code
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function sendOtp() {
    const btn = document.getElementById('send-otp-btn');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    
    fetch('{{ route("mfa.send.email") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('send-otp-section').classList.add('hidden');
            document.getElementById('verify-form').classList.remove('hidden');
            toastr.success(data.message);
        } else {
            toastr.error('Failed to send OTP');
        }
    })
    .catch(() => {
        toastr.error('Error sending OTP');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Send Verification Code';
    });
}
</script>
@endsection