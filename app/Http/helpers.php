<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use App\Notifications\InstantNotification;

/**
 * Get system name
 */
function cmsName()
{
    $cms_name = Setting::where('name', 'cms_name')->first();
    return isset($cms_name) ? $cms_name->value : config('app.name');
}

function getAuthRoles(){
    $user = Auth::user();
    $rolesString = implode(', ', $user->roles->pluck('name')->toArray());
    return $rolesString;
}

function favicon()
{
    $favicon = Setting::where('name', 'favicon')->first();
    return isset($favicon) ? asset('storage/' . $favicon->value) : asset('images/favicon.png');
}

/**
 * Slug to name
 *
 * @param $slug
 * @return string
 */
function slugToName($slug)
{
    return ucwords(preg_replace('/[-_]/', ' ', $slug));
}

/**
 * Return user types
 *
 * @return array
 */
function userTypes()
{
    return [
        '' => 'Select User Type',
        'user' => 'User',
        'client' => 'Client'
    ];
}

/**
 * User account status
 *
 * @param $status
 * @return array|mixed
 */
function accountStatus($status = null)
{
    $statuses = [
        '' => 'Select Status',
        'active' => 'Active',
        'de-active' => 'In Active'
    ];
    if ($status) {
        return $statuses[$status];
    }
    return $statuses;
}

/**
 * Project statuses
 *
 * @param null $status
 * @return array|mixed
 */
function projectStatuses($status = null)
{
    $statuses = [
        '' => 'Select Project Status',
        'pending' => 'Pending',
        'in-progress' => 'In Progress',
        'delivered' => 'Delivered'
    ];
    if ($status) {
        return $statuses[$status];
    }
    return $statuses;
}

function formatProjectStatus($status)
{
    $formattedStatus = '';
    $s = projectStatuses($status);
    switch ($status) {
        case 'pending':
            $formattedStatus = "<span class='m-badge m-badge--danger m-badge--wide'>{$s}</span>";
            break;
        case 'in-progress':
            $formattedStatus = "<span class='m-badge m-badge--warning m-badge--wide'>{$s}</span>";
            break;
        case 'delivered':
            $formattedStatus = "<span class='m-badge m-badge--success m-badge--wide'>{$s}</span>";
            break;
    }
    return $formattedStatus;
}

function formatInvoiceStatus($status)
{
    $formattedStatus = '';
    $s = slugToName($status);
    switch ($status) {
        case 'pending':
            $formattedStatus = "<span class='m-badge m-badge--danger m-badge--wide'>{$s}</span>";
            break;
        case 'processing':
        case 'processed':
            $formattedStatus = "<span class='m-badge m-badge--info m-badge--wide'>{$s}</span>";
            break;
        case 'partially_paid':
            $formattedStatus = "<span class='m-badge m-badge--warning m-badge--wide'>{$s}</span>";
            break;
        case 'paid':
            $formattedStatus = "<span class='m-badge m-badge--success m-badge--wide'>{$s}</span>";
            break;
    }
    return $formattedStatus;
}

/**
 * Project types
 *
 * @param null $type
 * @return array|mixed
 */
function projectTypes($type = null)
{
    $types = [
        'fixed' => 'Fixed price',
        'hourly' => 'Hourly basis'
    ];
    if ($type) {
        return $types[$type];
    }
    return $types;
}

function selectCurrencies($currency = null)
{
    $currencies = ['EURO' => '€', 'USD' => '$', 'PKR' => 'Rs', 'Pound' => '£', 'CAD' => 'CAD'];
    if ($currency) {
        return $currencies[$currency];
    }
    return $currencies;
}

/**
 * Currencies
 *
 * @param null $currency
 * @return array|mixed
 */
function currencies($currency = null)
{
    $currencies = ['EURO' => '€', 'USD' => '$', 'PKR' => 'PKR', 'Pound' => '£', 'GBP' => '£', 'CAD' => 'CAD'];
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

/**
 * Format date
 *
 * @param $date
 * @param $format
 * @return string
 */
function formatDate($date, $format = 'd M y')
{
    $setting_format = Setting::where('name', 'date_format')->first();
    if ($setting_format) {
        $format = $setting_format->value;
    }
    if (empty($date)) {
        return $date;
    }
    $date = Carbon::parse($date);
    if ($date->year == date('Y') && $format !== "D, d M y") {
        $format = 'd M';
    }
    return $date->format($format);
}

/**
 * @param $data
 *
 * @param string $key
 *
 * @param null $icon
 * @param string $label
 * @return string
 */
function getRandomColor() {
    $colors = ['bg-primary', 'bg-secondary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-dark'];
    $randomBgColor = $colors[array_rand($colors)];
    return $randomBgColor;
}

function checkRolehasPermission($rolePermissionName, $permissionName)
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

function wrapWithLabel($data, $key = '', $icon = null, $label = 'info')
{
    $html = '';
    if (is_object($data)) {
        foreach ($data as $record) {
            if (!empty($icon)) {
            }
            if (!empty($key)) {
                $html .= '<span class="m-badge m-badge--' . $label . '">' . $record->$key . '</span>';
            } else {
                $html .= '<span class="m-badge m-badge--' . $label . '">' . $record . '</span>';
            }
        }
    } else if (!empty($data) || $data == 0) {
        $html = '<span class="m-badge m-badge--' . $label . ' mb-0">' . $data . '</span>';
    }

    return (empty($html)) ? '-' : $html;
}

/**
 * Format user status
 */
function formatUserStatus($status)
{
    $title = ucwords(str_replace('-', ' ', $status));
    if ($status === 'active') {
        return '<span class="m-badge m-badge--wide m-badge--success">' . $title . '</span>';
    }
    return '<span class="m-badge m-badge--wide m-badge--danger">' . $title . '</span>';
}

/**
 * Storage path
 *
 * @param string $type
 *
 * @return string
 */
function getStoragePath(string $type): string
{
    $path = '';
    switch ($type) {
        case 'users':
            $path = 'images/users/';
            break;
        case 'company':
            $path = 'images/companies/';
            break;
        case 'invoice':
            $path = 'invoices';
            break;
    }
    return $path;
}

/**
 * @param $filename
 *
 * @return string
 */
function getFile($filename)
{
    return Storage::url($filename);
}

/**
 * Name to image
 *
 * @param $user
 * @return string
 */
function nameToImage($user)
{
    return substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1);
}

function getInitials(string $string)
{
    $words = explode(' ', $string);
    return strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
}

function fullName($user)
{
    return $user->first_name . ' ' . $user->last_name;
}

/**
 * Priority to icon
 *
 * @param $priority
 * @return string
 */
function priorityToIcon($priority)
{
    if ($priority == 'high') {
        return '<i data-toggle="tooltip" title="' . $priority . '" data-priority="1" class="la la-arrow-up text-red"></i>';
    } else if ($priority === 'medium') {
        return '<i data-toggle="tooltip" title="' . $priority . '" data-priority="2" class="la la-arrow-up text-orange"></i>';
    } else {
        return '<i data-toggle="tooltip" title="' . $priority . '" data-priority="3" class="la la-arrow-down text-green"></i>';
    }
}

/**
 * Priority to number
 *
 * @param $priority
 * @return mixed
 */
function priorityToNum($priority)
{
    $priorities = [
        'high' => 1,
        'medium' => 2,
        'low' => 3
    ];
    return $priorities[$priority];
}


/**
 * @param $attachment
 * @return string
 */
function showAttachment($attachment)
{
    if ($attachment) {
        $mimes = ['image/vnd.adobe.photoshop'];
        $mime_parts = explode('/', $attachment->mimes);
        $url = Storage::url($attachment->file);
        if ($mime_parts[0] == 'image' && !in_array($attachment->mimes, $mimes)) {
            return "<a href='{$url}' data-toggle='lightbox'><img style='max-width: 200px;' src='{$url}' alt=''/></a>";
        } else if ($mime_parts[0] == 'video') {
            return '<video width="320" height="240" controls>
				<source src="' . $url . '" type="' . $attachment->mimes . '">
				Your browser does not support the video tag.
		    </video>';
        } else {
            return "<a href='{$url}' target='_blank'><i class='la la-4x la-file'></i></a>";
        }
    }
    return '';
}

/**
 * Format time
 *
 * @param $time
 * @return string
 */
function formatTime($time)
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


/**
 * Checks if the object is present in the given collection
 *
 * @param $permissions
 * @param $permission
 * @return bool
 */
function isChecked($permissions, $permission)
{
    if (count($permissions)) {
        // Collection, not db query
        return !!$permissions->where('name', $permission->name)->first();
    }
    return false;
}

/**
 * Checks if current user is owner
 *
 * @param $id
 * @return bool
 */
function isOwner($id)
{
    return auth()->user()->id == $id;
}

function dateDiff($d1, $d2)
{
    $d1 = strtotime(date('Y-m-d', strtotime($d1)));
    $d2 = strtotime(date('Y-m-d', strtotime($d2)));
    $d = (int)floor(($d1 - $d2) / 86400);
    return $d;
}

function sendNotification(int $id, string $message, string $route)
{
    try {
        $user = User::findOrFail($id);
        $user->notify(new InstantNotification($id, $message, $route));
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }
}

function getAvatar($user)
{
    if (!empty($user->avatar)) {
        $url = Storage::url($user->avatar);
        return "<img src={$url} alt=''/>";
    } else {
        $avatar = nameToImage($user);
        return "<span>{$avatar}</span>";
    }
}
/////function for uuid
function getUuid()
{
    return bin2hex(random_bytes(6));
}

function addEllipsis($text, $max = 30)
{
    return strlen($text) > 30 ? mb_substr(strip_tags($text), 0, $max, "UTF-8") . "..." : $text;
}

function saveResizeImage($file, $directory, $width, $type = 'jpg', $height = null)
{
    if (!Storage::exists($directory)) {
        Storage::makeDirectory("$directory");
    }
    $is_preview = strpos($directory, 'previews') !== false;
    $filename = Str::random() . time() . '.' . $type;
    $path = "$directory/$filename";
    $manager = new ImageManager(
        new Intervention\Image\Drivers\Gd\Driver()
    );

    $img = $manager->make($path)->resize($width, $height, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });

    if ($width == $is_preview) {
        $img = $img->blur(60);
    }

    $resource = $img->stream()->detach();
    Storage::disk('public')->put($path, $resource, 'public');
    return $path;
}

function deleteFile($path)
{
    if (!empty($path) && file_exists('app/'.$path)) {
        unlink(storage_path('app/'.$path));
    }

    $storage_path = 'storage/' . $path;
    $public_path = public_path($storage_path);
    if (!empty($path) && file_exists($public_path)) {
        unlink($public_path);
    }
}

function getSlug($url)
{
    $path = parse_url($url, PHP_URL_PATH);
    preg_match('/[^\/]+$/', $path, $matches);
    $slug = isset($matches[0]) ? $matches[0] : '/';
    return $slug;
}

/**
 * Nae to slug
 *
 * @param $name
 * @return string
 */
function nameToSlug($name)
{
    return Str::slug($name);
}
