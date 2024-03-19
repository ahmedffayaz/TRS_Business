<?php

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * Get system name
 */
function cmsName() : string
{
    $cms_name = Setting::where('name', 'cms_name')->first();
    return isset($cms_name) ? $cms_name->value : config('app.name');
}

function getAuthRoles(){
    $user = Auth::user();
    $rolesString = implode(', ', $user->roles->pluck('name')->toArray());
    return $rolesString;
}

function favicon() : string
{
    $favicon = Setting::where('name', 'favicon')->first();
    return isset($favicon) ? asset('storage/' . $favicon->value) : asset('images/favicon.png');
}

//function for uuid
function getUuid()
{
    return bin2hex(random_bytes(6));
}

function getSlug($url)
{
    $path = parse_url($url, PHP_URL_PATH);
    preg_match('/[^\/]+$/', $path, $matches);
    $slug = isset($matches[0]) ? $matches[0] : '/';
    return $slug;
}

function nameToSlug($name) : string
{
    return Str::slug($name);
}

function status($status) : string
{
    switch ($status->name) {
        case 'SUCCESS':
            return 'success';
        case 'PENDING':
            return 'warning';
        case 'DELIVERED':
            return 'success';
        default:
            return 'primary';
    }
}

function formatDate($date, $format = 'd M y')
{
    $business = Business::whereName(session('business'))->first();

    // Check $business & business date_format column not null
    if ($business && $business->date_format) $format = $business->date_format;

    return empty($date) ? $date : Carbon::parse($date)->format($format);

}

// Currencies
function currencies($currency = null) : array
{
    $currencies = ['EURO' => '€', 'USD' => '$', 'PKR' => 'Rs', 'Pound' => '£', 'CAD' => 'CAD'];
    if ($currency) {
        return $currencies[$currency];
    }
    return $currencies;
}

function currencyToShortName($currency = null)
{
    $currencies = ['EURO' => 'EUR', 'USD' => 'USD', 'PKR' => 'PKR', 'Pound' => 'GBP', 'GBP' => 'GBP', 'CAD' => 'CAD'];
    if ($currency) {
        return $currencies[$currency];
    }
    return $currency;
}

function formatCurrency($amount, $currency = "EUR")
{
    if ($amount == 0) {
        return '-';
    }
    $formatter = new \NumberFormatter('en-US', NumberFormatter::CURRENCY);
    $currency = currencyToShortName($currency);
    return $formatter->formatCurrency($amount, $currency);
}

function randomColors()
{
    $colors = ['success', 'danger', 'primary', 'info', 'secondary'];

     // Select a random color from the array
     return $colors[array_rand($colors)];
}

function getUserAvatar($user)
{
    return $user?->avatar ? asset('storage/' . $user?->avatar) : '/assets/images/avatar.png';
}
