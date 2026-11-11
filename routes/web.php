<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn()=> redirect('/app'));
Route::get('/admin/login', fn()=> redirect('/masuk'));

Route::get('/masuk', fn() => view('auth'));
Route::get('/daftar', fn() => view('auth'));
Route::get('/app', fn() => view('app'));
Route::get('/test', fn() => view('test'));
Route::get('/admin', function () {
    return view('admin');
});
Route::get('/safety-hubs-ui', function () {
    return view('safety_hubs');
});
Route::get('/rute-saya', function () {
    return view('rute_saya');
});
Route::get('/lapor-kondisi', function () {
    return view('lapor_kondisi');
});