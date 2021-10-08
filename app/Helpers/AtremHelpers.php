<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// GENERATE
function generateDigit($id){
    return ($id < 10) ? '00'.$id : ($id < 100 ? '0'.$id : $id);
}


// RESPONSE API
function responseApi($status, $data, $msg){
    return response()->json([
        'status' => $status,
        'data' => $data,
        'msg' => $msg
    ]);
}

// FORMAT

function format_rupiah($str) {
    if ( ! is_numeric($str)) return false;
    return "IDR. ".number_format($str,0,',','.');
}

function format_date($date, $dayname = false) {
    if ($dayname) return Carbon::parse($date)->translatedFormat('l, d F Y');
    return Carbon::parse($date)->translatedFormat('d F Y');
}

function format_datetime($date) {
    return Carbon::parse($date)->translatedFormat('d F Y H:i:s')." WIB";
}

// SELECT DATA

function returnStatus($key = 'showall'){
    $arr = ['active'=>'Active', 'inactive'=>'Inactive'];
    if ($key == 'showall') return $arr;
    if (array_key_exists($key, $arr)) return $arr[$key];
    return "No Data.";
}

// RANDOM
function set_active_menu($uri, $class = "active") {
    if (is_array($uri)) {
        foreach($uri as $val) {
            if (Route::is($val)) return $class;
        }
    } else {
        if (Route::is($uri)) return $class;
    }
}
