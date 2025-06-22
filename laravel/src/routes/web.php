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
Route::middleware(['auth', 'role:admin'])->group(function () {
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
    Route::get('/activeusers',function () { return view('routeros.activeuser');})->name('activeuser');
    Route::get('/getactiveusers',[RouterOsController::class,'showActiveUser'])->name('get.active.users');
    Route::post('/mac', [RouterOsController::class,'addMaccBinding'])->name('post.mac');
    Route::get('/mac/{id}', [RouterOsController::class,'getMacAdd'])->name('get.mac.by.id');
    Route::post('/mac/{id}', [RouterOsController::class,'updateMacBinding'])->name('update.mac.by.id');
    Route::delete('/mac/{id}', [RouterOsController::class,'deleteMacBinding'])->name('delete.mac.by.id');
    Route::get('/guest/{mac}', [GuestController::class,'show'])->name('show.guest.by.mac');
    Route::get('/user/profile',[RouterOsController::class,'getUserProfiles'])->name('get.user.profile');
    Route::post('/user/profile',[RouterOsController::class,'addUserProfile'])->name('post.user.profile');
    Route::post('/user/profile/{id}',[RouterOsController::class,'updateUserProfile']);
    Route::get('/profiles', function () { return view('routeros.userprofile'); })->name('user.profile');
    Route::get('/profiles/{id}', [RouterOsController::class,'getUserProfile']);
    Route::delete('/profiles/{id}', [RouterOsController::class,'deleteUserprofile']);

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
Route::get('/userprofile',[RouterOsController::class,'showUserProfile']);
Route::get('/fetch', function () { return view('test.form'); });
Route::post('/fetch',[RouterOsController::class,'testPost'])->name('test.post');





