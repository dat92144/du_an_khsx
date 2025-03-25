<?php
use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('app'); // Trả về file Blade chính chứa Vue
})->where('any', '.*'); // Chấp nhận mọi route và cho Vue xử lý
