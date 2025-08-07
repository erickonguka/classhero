<?php

namespace App\Services;

class CurrencyService
{
    private static $exchangeRates = [
        'USD' => 1.0,
        'EUR' => 0.85,
        'GBP' => 0.73,
        'JPY' => 110.0,
        'CAD' => 1.25,
        'AUD' => 1.35,
        'CNY' => 6.45,
        'INR' => 74.5,
        'BRL' => 5.2,
        'ZAR' => 14.8,
        'NGN' => 411.0,
        'KES' => 108.0,
        'EGP' => 15.7,
        'SAR' => 3.75,
        'AED' => 3.67,
        'SGD' => 1.35,
        'MYR' => 4.15,
        'THB' => 33.0,
        'PHP' => 50.5,
        'IDR' => 14250.0,
        'KRW' => 1180.0,
        'TWD' => 28.0,
        'HKD' => 7.8,
        'PKR' => 176.0
    ];

    private static $currencySymbols = [
        'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥', 'CAD' => 'C$',
        'AUD' => 'A$', 'CNY' => '¥', 'INR' => '₹', 'BRL' => 'R$', 'ZAR' => 'R',
        'NGN' => '₦', 'KES' => 'KSh', 'EGP' => 'E£', 'SAR' => 'SR', 'AED' => 'د.إ',
        'SGD' => 'S$', 'MYR' => 'RM', 'THB' => '฿', 'PHP' => '₱', 'IDR' => 'Rp',
        'KRW' => '₩', 'TWD' => 'NT$', 'HKD' => 'HK$', 'PKR' => 'Rs'
    ];

    public static function convert($amount, $fromCurrency = 'USD', $toCurrency = 'USD')
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $usdAmount = $amount / self::$exchangeRates[$fromCurrency];
        return $usdAmount * self::$exchangeRates[$toCurrency];
    }

    public static function getSymbol($currency)
    {
        return self::$currencySymbols[$currency] ?? '$';
    }

    public static function formatPrice($amount, $currency = 'USD')
    {
        $symbol = self::getSymbol($currency);
        return $symbol . number_format($amount, 2);
    }

    public static function detectCurrencyFromCountry($countryCode)
    {
        $countryToCurrency = [
            'US' => 'USD', 'CA' => 'CAD', 'GB' => 'GBP', 'AU' => 'AUD',
            'DE' => 'EUR', 'FR' => 'EUR', 'IT' => 'EUR', 'ES' => 'EUR', 'NL' => 'EUR',
            'JP' => 'JPY', 'CN' => 'CNY', 'IN' => 'INR', 'BR' => 'BRL',
            'ZA' => 'ZAR', 'NG' => 'NGN', 'KE' => 'KES', 'EG' => 'EGP',
            'SA' => 'SAR', 'AE' => 'AED', 'SG' => 'SGD', 'MY' => 'MYR',
            'TH' => 'THB', 'PH' => 'PHP', 'ID' => 'IDR', 'KR' => 'KRW',
            'TW' => 'TWD', 'HK' => 'HKD', 'PK' => 'PKR'
        ];

        return $countryToCurrency[$countryCode] ?? 'USD';
    }
}