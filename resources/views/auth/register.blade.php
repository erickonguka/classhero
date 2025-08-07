@extends('layouts.app')

@section('title', 'Register')

@push('styles')
<style>
.iti {
    width: 100% !important;
    position: relative;
    display: block !important;
}
.iti input, .iti__tel-input {
    width: 100% !important;
    padding-right: 52px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    padding: 0.75rem 1rem !important;
    font-size: 1rem !important;
    line-height: 1.5rem !important;
    box-sizing: border-box !important;
}
.dark .iti input, .dark .iti__tel-input {
    border-color: #4b5563 !important;
    background-color: #374151 !important;
    color: white !important;
}
.iti__flag-container {
    position: absolute;
    top: 1px;
    bottom: 1px;
    right: 1px;
    padding: 0;
    border-radius: 0 0.5rem 0.5rem 0;
}
.iti__selected-flag {
    height: 100%;
    padding: 0 8px;
    background-color: transparent;
    border: none;
    border-radius: 0 0.5rem 0.5rem 0;
    display: flex;
    align-items: center;
}
.iti__arrow {
    margin-left: 6px;
    width: 0;
    height: 0;
    border-left: 3px solid transparent;
    border-right: 3px solid transparent;
    border-top: 4px solid #555;
}
.iti__country-list {
    position: absolute;
    z-index: 1001;
    list-style: none;
    text-align: left;
    padding: 0;
    margin: 0 0 0 -1px;
    box-shadow: 1px 1px 4px rgba(0,0,0,0.2);
    background-color: white;
    border: 1px solid #CCC;
    white-space: nowrap;
    max-height: 200px;
    overflow-y: scroll;
    border-radius: 8px;
    display: none;
}
.iti__country-list.iti__country-list--dropup {
    bottom: 100%;
    margin-bottom: -1px;
}
.iti__flag-box {
    display: inline-block;
    width: 20px;
}
.iti__country {
    padding: 5px 10px;
    outline: none;
}
.iti__country.iti__highlight {
    background-color: rgba(0,0,0,0.05);
}
.iti__country-name {
    margin-left: 6px;
}
.iti__dial-code {
    color: #999;
}
.dark .iti__country-list {
    background-color: #374151;
    border-color: #4B5563;
}
.dark .iti__country {
    color: #F9FAFB;
}
.dark .iti__country.iti__highlight {
    background-color: #4B5563;
}
.dark .iti__arrow {
    border-top-color: #9CA3AF;
}
</style>
@endpush



@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-100 to-purple-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                <span class="text-white font-bold text-xl">CH</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Create account</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Join ClassHero and start learning today</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Full Name
                    </label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                           placeholder="Enter your full name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                           placeholder="Enter your email">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Phone Number
                    </label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                    <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code') }}">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        I want to
                    </label>
                    <select id="role" name="role" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                        <option value="">Select your role</option>
                        <option value="learner" {{ old('role') == 'learner' ? 'selected' : '' }}>Learn (Student)</option>
                        <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teach (Instructor)</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password
                    </label>
                    <input id="password" name="password" type="password" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                           placeholder="Create a password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Confirm Password
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                           placeholder="Confirm your password">
                </div>

                <!-- Preferred Categories (for learners) -->
                <div id="categories-section" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Preferred Learning Topics (Optional)
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(\App\Models\Category::where('is_active', true)->get() as $category)
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_categories[]" value="{{ $category->id }}"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Terms -->
                <div class="flex items-start">
                    <input type="checkbox" required
                           class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                        I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and 
                        <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                    </span>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.querySelector('#phone');
    const countryCodeInput = document.querySelector('#country_code');
    
    if (phoneInput) {
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "auto",
            separateDialCode: true,
            formatOnDisplay: true,
            nationalMode: false,
            autoPlaceholder: "aggressive",
            geoIpLookup: function(callback) {
                fetch('https://ipapi.co/json/')
                    .then(res => res.json())
                    .then(data => {
                        const countryCode = (data && data.country_code) ? data.country_code.toLowerCase() : 'us';
                        callback(countryCode);
                        setTimeout(() => {
                            const selectedCountry = iti.getSelectedCountryData();
                            if (selectedCountry) {
                                countryCodeInput.value = selectedCountry.iso2.toUpperCase();
                                updateCurrency(selectedCountry.iso2.toUpperCase());
                            }
                        }, 100);
                    })
                    .catch(() => {
                        callback('us');
                        setTimeout(() => {
                            countryCodeInput.value = 'US';
                            updateCurrency('US');
                        }, 100);
                    });
            },
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
        });
        
        phoneInput.addEventListener('countrychange', function() {
            const countryData = iti.getSelectedCountryData();
            if (countryData) {
                countryCodeInput.value = countryData.iso2.toUpperCase();
                updateCurrency(countryData.iso2.toUpperCase());
            }
        });
        
        function updateCurrency(countryCode) {
            const currencyMap = {
                'US': 'USD', 'CA': 'CAD', 'GB': 'GBP', 'AU': 'AUD', 'NZ': 'NZD',
                'DE': 'EUR', 'FR': 'EUR', 'IT': 'EUR', 'ES': 'EUR', 'NL': 'EUR',
                'JP': 'JPY', 'CN': 'CNY', 'IN': 'INR', 'BR': 'BRL', 'MX': 'MXN',
                'ZA': 'ZAR', 'NG': 'NGN', 'KE': 'KES', 'EG': 'EGP', 'MA': 'MAD',
                'RU': 'RUB', 'TR': 'TRY', 'SA': 'SAR', 'AE': 'AED', 'KW': 'KWD',
                'SG': 'SGD', 'MY': 'MYR', 'TH': 'THB', 'PH': 'PHP', 'ID': 'IDR',
                'VN': 'VND', 'KR': 'KRW', 'TW': 'TWD', 'HK': 'HKD', 'PK': 'PKR'
            };
            
            const currency = currencyMap[countryCode] || 'USD';
            let currencyInput = document.querySelector('input[name="currency"]');
            
            if (!currencyInput) {
                currencyInput = document.createElement('input');
                currencyInput.type = 'hidden';
                currencyInput.name = 'currency';
                document.querySelector('form').appendChild(currencyInput);
            }
            
            currencyInput.value = currency;
        }
        
        // Force intl-tel-input to match other input widths
        setTimeout(() => {
            const itiContainer = document.querySelector('.iti');
            if (itiContainer) {
                itiContainer.classList.add('w-full');
                itiContainer.style.width = '100%';
                itiContainer.style.display = 'block';
            }
        }, 200);
    }

    document.getElementById('role').addEventListener('change', function() {
        const categoriesSection = document.getElementById('categories-section');
        if (this.value === 'learner') {
            categoriesSection.classList.remove('hidden');
        } else {
            categoriesSection.classList.add('hidden');
        }
    });
});
</script>
@endsection