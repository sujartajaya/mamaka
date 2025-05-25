<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebloginController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RouterOsController;

Route::get('/', function () {return view('home.home');})->name('home');
Route::get('/os', [GuestController::class,'index']);
Route::get('/web/login', function () { return view('weblogin.loginv1');});

Route::get('/web/countries', [CountryController::class,'show'])->name('country');
Route::post('/web/login',[WebloginController::class,'create'])->name('weblogin');
Route::post('/web/login/store',[WebloginController::class,'store']);
Route::get('/login', function () { $prev_url = url()->previous(); return view('user.loginv1',compact('prev_url'));})->name('login');
Route::post('/login',[UserController::class,'authtenticate'])->name('authtenticate');

/** user admin type */
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/register', function () { return view('user.register');})->name('register');
    Route::post('/register',[UserController::class,'store'])->name('postregister');
    Route::get('/getusers', [UserController::class,'getUsers'])->name('users');
    // Route::get('/users', function () { return view('user.users');});
    Route::get('/users', [UserController::class,'userlists']);
    Route::post('/user',[UserController::class,'save'])->name('adduser');
    Route::get('/guests', [WebloginController::class,'getGuests'])->name('getguests');
    Route::get('/tools', function () {return view('home.tool');})->name('tools');
    Route::get('/mac', function () { return view('routeros.macbinding'); })->name('mac');
    Route::get('/macbinding', [RouterOsController::class,'showMacBind'])->name('macbinding');
    Route::post('/mac', [RouterOsController::class,'addMaccBinding']);
    Route::get('/mac/{id}', [RouterOsController::class,'getMacAdd']);
    Route::post('/mac/{id}', [RouterOsController::class,'updateMacBinding']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout',[UserController::class,'logout'])->name('logout');
});
/** testing */
Route::get('/testpage', function () { return view('test.tablev5');});
Route::get('/testusers', function () { return view('user.users');});
Route::get('/testguest',[GuestController::class,'displaydata']);
Route::get('/testgetusers', [UserController::class,'getUsers']);
Route::get('/table', function () { return view('test.table');});
Route::get('/card', function () { return view('test.card');});
Route::get('/active', [RouterOsController::class,'showActiveUser']);






