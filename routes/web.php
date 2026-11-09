<?php
use Illuminate\Support\Facades\Route;
Route::get('/', fn()=> redirect('/test'));
Route::get('/test', fn()=> view('test'));
Route::get('/admin/login', fn()=> view('admin-login'));
