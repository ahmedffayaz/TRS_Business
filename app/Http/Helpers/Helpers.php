<?php

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Business;
use Illuminate\Support\Str;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

/**
 * Get system name
 */
function cmsName() : string
{
    $cmsName = Setting::where('name', 'cms_name')->first();
    return isset($cmsName) ? $cmsName->value : config('app.name');
}

function cmsLogo() : string
{
    $cmsLogo = Setting::where('name', 'cms_logo')->first();
    return isset($cmsLogo) && Storage::disk('public')->exists('cms/images/' . $cmsLogo->value)
        ? asset('storage/cms/images/' . $cmsLogo->value)
        : asset('trs_logo.svg');
}

function favicon($favicon = null)
{
    if ($favicon && Auth::check()) {
        return $favicon && Storage::disk('public')->exists($favicon) ? asset('storage/' . $favicon) : asset('trs_logo.svg');
    }

    $cmsFavicon = Setting::where('name', 'cms_favicon')->first();
    return isset($cmsFavicon) && Storage::disk('public')->exists('cms/images/' . $cmsFavicon->value)
        ? asset('storage/cms/images/' . $cmsFavicon->value)
        : asset('trs_logo.svg');
}

function getAuthRoles($businessId){
    $user = Auth::user();
    $rolesString = implode(', ', $user->roles->where('business_id', $businessId)->pluck('name')->toArray());
    return $rolesString;
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
function currencies($currency = null) : array|string
{
    $currencies = ['EURO' => '€', 'USD' => '$', 'PKR' => 'Rs', 'Pound' => '£', 'GBP' => '£', 'CAD' => 'CAD'];;
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

function formatCurrency($amount, $currency = "EURO")
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
    $colors = ['success', 'danger', 'primary', 'info', 'secondary', 'warning', 'dark'];

     // Select a random color from the array
     return $colors[array_rand($colors)];
}

function getUserAvatar($user)
{
    return $user?->avatar ? asset('storage/' . $user?->avatar) : '/assets/images/avatar.png';
}

function checkRoleHasPermission($rolePermissionName, $permissionName)
{
    return in_array($permissionName, $rolePermissionName);
}

function getGroupPermissions()
{
    $permissionGroups = Permission::pluck('group')->unique()->toArray();
    $permissionArray = [
        'group' => [],
        'title' => [],
    ];

    foreach ($permissionGroups as $groupName) {
        $permissionArray['group'][] = $groupName;
        $permissionArray['title'][$groupName] = Permission::where('group', $groupName)
            ->pluck('title')
            ->toArray();
    }

    return $permissionArray;
}

function priorityToIcon($priority)
{
    if ($priority == 'high') {
        return '<i data-toggle="tooltip" title="' . $priority . '" data-priority="1" data-feather="arrow-up" class="text-success"></i>';
    } else if ($priority === 'medium') {
        return '<i data-toggle="tooltip" title="' . $priority . '" data-priority="2" data-feather="arrow-up" class="text-warning"></i>';
    } else {
        return '<i data-toggle="tooltip" title="' . $priority . '" data-priority="3" data-feather="arrow-down" class="text-danger"></i>';
    }
}

function priorityToNum($priority)
{
    $priorities = [
        'high' => 1,
        'medium' => 2,
        'low' => 3
    ];
    return $priorities[$priority];
}

function slugToName($slug) : string
{
    return ucwords(preg_replace('/[-_]/', ' ', $slug));
}

function formatTime($time) : string
{
    if (!empty($time)) {
        if ($time >= 60) {
            $minutes = $time % 60;
            if ($minutes > 0) {
                return ($time - $minutes) / 60 . ' hrs ' . $minutes . ' mins';
            }
            return $time / 60 . ' hrs';
        }
        return $time . ' mins';
    }
    return '-';
}

function timeSpent($comments)
{
    $time = array_sum(array_column($comments, 'time'));
    return formatTime($time);
}

function convertMinutesToHours($minutes)
{
    return !empty($minutes) ? ($minutes / 60) : 0;
}

function formatInvoiceStatus($status) : string
{
    $formattedStatus = '';
    $s = slugToName($status);
    switch ($status) {
        case 'pending':
            $formattedStatus = "<span class='badge rounded-pill badge-light-danger'>{$s}</span>";
            break;
        case 'processing':
            $formattedStatus = "<span class='badge rounded-pill badge-light-info'>{$s}</span>";
            break;
        case 'processed':
            $formattedStatus = "<span class='badge rounded-pill badge-light-info'>{$s}</span>";
            break;
        case 'partially_paid':
            $formattedStatus = "<span class='badge rounded-pill badge-light-warning'>{$s}</span>";
            break;
        case 'paid':
            $formattedStatus = "<span class='badge rounded-pill badge-light-success'>{$s}</span>";
            break;
         case 'draft':
             $formattedStatus = "<span class='badge rounded-pill badge-light-primary'>{$s}</span>";
             break;

    }
    return $formattedStatus;
}

function formatTaskCompletedStatus($status) : string
{
    $formattedStatus = '';
    $s = slugToName($status);
    switch ($status) {
        case 'completed':
            $formattedStatus = "<span class='badge rounded-pill badge-light-success'>{$s}</span>";
            break;
        case 'active':
            $formattedStatus = "<span class='badge rounded-pill badge-light-warning'>{$s}</span>";
            break;
    }
    return $formattedStatus;
}

// Storage path
function getStoragePath(string $type): string
{
    $path = '';
    switch ($type) {
        case 'users':
            $path = 'images/users/';
            break;
        case 'client':
            $path = 'images/client/';
            break;
        case 'business':
            $path = 'images/business/';
            break;
        case 'invoice':
            $path = 'invoices';
            break;
    }
    return $path;
}


function getInvoiceRecord($invoice_id)
{
    $invoice = Invoice::find($invoice_id);
    return  $invoice;
}

function getSiteLogo($path = null)
{
    return !empty($path) ? asset('storage/' . $path) : asset('trs_logo.svg');
}

function emailTemplate($key, $details, $filteredKeywords = [], $filteredKeywordsValue = [])
{
    $emailTemplate = EmailTemplate::where('key', $key)->first();

    $variables = ['{{SITE_TITLE}}', '{{SITE_URL}}', '{{SUBJECT}}, {{BUSINESS}}'];
    $variablesMerge = array_merge($variables, $filteredKeywords);

    $data = ['The Right software', url('/'), $emailTemplate->subject, $details['business_name']];
    $dataMerge = array_merge($data, $filteredKeywordsValue);

    if ($filteredKeywords && $filteredKeywordsValue) {
        $filteredMessage  = str_replace($variablesMerge, $dataMerge, $emailTemplate->body);
    } else {
        $filteredMessage  = str_replace($variables, $data, $emailTemplate->body);
    }

    return array(
        'title' => $emailTemplate->title,
        'message' => $filteredMessage,
        'subject' => $emailTemplate->subject
    );
}

function leaveStatus($status)
{
    switch ($status->name) {
        case 'APPROVED':
            return 'success';
        case 'PENDING':
            return 'warning';
        case 'REJECTED':
            return 'danger';
        case 'CANCELED':
            return 'danger';
    }
}

function getFullName($user)
{
    return ucwords("{$user->first_name} {$user->last_name}");
}

function getInitials($input)
{
    if (is_object($input) && isset($input->first_name) && isset($input->last_name)) {
        // Assume input is a user object
        $firstName = $input->first_name;
        $lastName = $input->last_name;

        $initials = ($firstName ? $firstName[0] : '') . ($lastName ? $lastName[0] : '');
    } elseif (is_string($input)) {
        // Assume input is a project name string
        $words = explode(' ', $input);
        $initials = '';

        if (count($words) > 1) {
            $initials = $words[0][0] . $words[count($words) - 1][0];
        } elseif (count($words) === 1) {
            $initials = $words[0][0];
        }
    } else {
        // Handle unexpected input
        $initials = '';
    }

    return strtoupper($initials);
}
