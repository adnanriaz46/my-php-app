<?php

namespace App\Helper;

use Carbon\Carbon;

class ValueFormat
{
    // Number Formatting
    public static function formatNumber($value, int $decimal = 0): ?string
    {
        if (is_null($value) || $value === '') return null;

        return number_format((float)$value, $decimal, '.', ',');
    }

    public static function formatPrice($value, int $decimal = 0, string $currency = 'USD'): string
    {
        if (is_null($value) || $value === '') return '';

        $symbol = self::currencySymbol($currency);
        return $symbol . number_format((float)$value, $decimal, '.', ',');
    }

    public static function formatPercent($value, int $maxDigit = 0): string
    {
        if (is_null($value) || $value === '') return '';
        return number_format((float)$value * 100, $maxDigit) . '%';
    }

    public static function formatPhone($value): string
    {
        $cleaned = preg_replace('/\D/', '', $value);
        if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $cleaned, $matches)) {
            return "($matches[1]) $matches[2]-$matches[3]";
        }
        return $value;
    }

    // Date Formatting
    public static function formatDate($value, string $format = 'M d, Y'): string
    {
        return Carbon::parse($value)->format($format);
    }

    public static function formatDateTime($value, string $format = 'M d, Y h:i A'): string
    {
        return Carbon::parse($value)->format($format);
    }

    public static function formatGetDays($value): int
    {
        $inputDate = Carbon::parse($value)->startOfDay();
        $now = Carbon::now('America/New_York')->startOfDay();
        return $now->diffInDays($inputDate);
    }

    // Text Formatting
    public static function formatToCapitalizeEachWord(?string $text): string
    {
        return ucwords(strtolower($text ?? ''));
    }

    public static function formatToCapitalize(?string $text): string
    {
        return ucfirst(strtolower($text ?? ''));
    }

    public static function formatToUpperCase(?string $text): string
    {
        return strtoupper($text ?? '');
    }

    public static function formatToLowerCase(?string $text): string
    {
        return strtolower($text ?? '');
    }

    public static function formatToTruncate(?string $text, int $length = 50, string $suffix = '...'): string
    {
        if (!$text) return '';
        return strlen($text) > $length ? substr($text, 0, $length) . $suffix : $text;
    }

    public static function formatUserAgent(string $ua): string
    {
        preg_match('/(Chrome|Firefox|Safari|Edg|Opera)\/([\d.]+)/', $ua, $browserMatch);
        preg_match('/\(([^)]+)\)/', $ua, $osMatch);

        $browser = $browserMatch ? $browserMatch[1] . ' ' . explode('.', $browserMatch[2])[0] : 'Unknown Browser';
        $osRaw = $osMatch[1] ?? 'Unknown OS';

        if (str_contains($osRaw, 'Windows NT 10.0')) $os = 'Windows 10';
        elseif (str_contains($osRaw, 'Mac OS X')) $os = 'macOS';
        elseif (str_contains($osRaw, 'Android')) $os = 'Android';
        elseif (str_contains($osRaw, 'iPhone')) $os = 'iOS';
        else $os = 'Unknown OS';

        return "$browser on $os";
    }

    protected static function currencySymbol(string $currency): string
    {
        return match (strtoupper($currency)) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            default => strtoupper($currency) . ' ',
        };
    }

}
