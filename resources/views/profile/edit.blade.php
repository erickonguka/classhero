@extends('layouts.app')

@section('title', 'Edit Profile')

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
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Profile</h1>
        
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="text-center">
                <div class="relative inline-block">
                    @if($user->getProfilePictureUrl())
                        <img id="profile-preview" src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                    @else
                        <div id="profile-preview" class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center border-4 border-gray-200">
                            <span class="text-3xl font-bold text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <label for="profile_picture" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </label>
                </div>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden" onchange="previewImage(this)">
                @error('profile_picture')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code', $user->country_code) }}">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                <textarea id="bio" name="bio" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-between">
                <a href="{{ route('profile.show') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
    
    <!-- Delete Account Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mt-6 border border-red-200 dark:border-red-800">
        <h2 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">Delete Account</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
        </p>
        
        <button onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            Delete Account
        </button>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Confirm Account Deletion</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            Are you sure you want to delete your account? This action cannot be undone.
        </p>
        
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                <input type="password" id="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profile-preview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview" class="w-32 h-32 rounded-full object-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function confirmDelete() {
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}
</script>

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