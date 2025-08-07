@extends('layouts.app')

@section('title', 'Checkout - ' . $course->title)

@section('content')
<div class="bg-gradient-to-br from-green-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Course Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Order Summary</h2>
                
                <div class="flex items-start space-x-4 mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex-shrink-0"></div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $course->title }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $course->short_description }}</p>
                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ $course->lessons->count() }} lessons</span>
                            @if($course->duration_hours)
                                <span>{{ $course->duration_hours }} hours</span>
                            @endif
                            <span class="capitalize">{{ $course->difficulty }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Course Price</span>
                        <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($course->price, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Platform Fee</span>
                        <span class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($course->price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">30-Day Money Back Guarantee</p>
                            <p class="text-xs text-green-600 dark:text-green-400">Full refund if you're not satisfied</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Payment Details</h2>

                <form action="{{ route('payment.process', $course) }}" method="POST" id="payment-form">
                    @csrf

                    <!-- Payment Method -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Payment Method</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <input type="radio" name="payment_method" value="card" checked 
                                       class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <div class="ml-3 flex items-center">
                                    <svg class="w-8 h-5 mr-2" viewBox="0 0 32 20" fill="none">
                                        <rect width="32" height="20" rx="4" fill="#1E40AF"/>
                                        <rect x="6" y="8" width="20" height="2" fill="white"/>
                                        <rect x="6" y="12" width="8" height="1" fill="white"/>
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">Credit/Debit Card</span>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <input type="radio" name="payment_method" value="paypal" 
                                       class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <div class="ml-3 flex items-center">
                                    <svg class="w-8 h-5 mr-2" viewBox="0 0 32 20" fill="none">
                                        <rect width="32" height="20" rx="4" fill="#0070BA"/>
                                        <path d="M8 6h4c2 0 3 1 3 3s-1 3-3 3h-2l-1 2H7l2-8z" fill="white"/>
                                        <path d="M12 8h4c2 0 3 1 3 3s-1 3-3 3h-2l-1 2h-2l2-8z" fill="white" opacity="0.7"/>
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">PayPal</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Card Details -->
                    <div id="card-details" class="space-y-4">
                        <div>
                            <label for="card_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cardholder Name</label>
                            <input type="text" id="card_name" name="card_name" 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   placeholder="John Doe" required>
                        </div>

                        <div>
                            <label for="card_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Number</label>
                            <input type="text" id="card_number" name="card_number" 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   placeholder="1234 5678 9012 3456" maxlength="19" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="card_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
                                <input type="text" id="card_expiry" name="card_expiry" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                       placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div>
                                <label for="card_cvc" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CVC</label>
                                <input type="text" id="card_cvc" name="card_cvc" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                       placeholder="123" maxlength="4" required>
                            </div>
                        </div>
                    </div>

                    <!-- PayPal Details (Hidden) -->
                    <div id="paypal-details" class="hidden">
                        <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg text-center">
                            <p class="text-blue-800 dark:text-blue-200">You will be redirected to PayPal to complete your payment.</p>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="mt-6">
                        <label class="flex items-start">
                            <input type="checkbox" required 
                                   class="mt-1 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and 
                                <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submit-payment" 
                            class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-lg font-semibold text-lg transition-colors">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Complete Payment - ${{ number_format($course->price, 2) }}
                        </span>
                    </button>
                </form>

                <div class="mt-4 flex items-center justify-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Secure Payment
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        SSL Encrypted
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Payment method toggle
    $('input[name="payment_method"]').on('change', function() {
        if ($(this).val() === 'card') {
            $('#card-details').show();
            $('#paypal-details').hide();
            $('#card-details input').attr('required', true);
        } else {
            $('#card-details').hide();
            $('#paypal-details').show();
            $('#card-details input').attr('required', false);
        }
    });

    // Card number formatting
    $('#card_number').on('input', function() {
        let value = $(this).val().replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        $(this).val(formattedValue);
    });

    // Expiry date formatting
    $('#card_expiry').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        $(this).val(value);
    });

    // CVC validation
    $('#card_cvc').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/gi, '');
        $(this).val(value);
    });

    // Form submission
    $('#payment-form').on('submit', function(e) {
        $('#submit-payment').prop('disabled', true).html(`
            <span class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                Processing Payment...
            </span>
        `);
    });
});
</script>
@endpush