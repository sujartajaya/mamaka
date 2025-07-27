<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebloginController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RouterOsController;
use App\Http\Controllers\TelegramController;

Route::get('/', function () {return view('home.home');})->name('home');
Route::get('/os', [GuestController::class,'index']);
Route::get('/web/login', function () { return redirect('https://ovolohotels.com/mamaka/long-stay/?gad_source=1&gad_campaignid=10952323866&gbraid=0AAAAADv4kheTZBXGJ3XMD38kkB7ImgCQD&gclid=Cj0KCQjwjdTCBhCLARIsAEu8bpI54UUiTSvQEVr5cIV1WbWTi7Cz5CuVRrphyl-Xlx3sKDEYi9eqx6oaAiglEALw_wcB');});

Route::get('/web/countries', [CountryController::class,'show'])->name('country');
Route::post('/web/login',[WebloginController::class,'create'])->name('weblogin');
Route::post('/web/login/store',[WebloginController::class,'store']);
Route::get('/login', function () { $prev_url = url()->previous(); return view('user.loginv1',compact('prev_url'));})->name('login');
Route::post('/login',[UserController::class,'authtenticate'])->name('authtenticate');

/** Telegram */
Route::get('/telegram/user/{id}',[TelegramController::class,'getTelegramUser']);
Route::post('/telegram/user',[TelegramController::class,'register']);
Route::get('/telegram/csv/macbinding',[TelegramController::class,'downloadMacBinding']);
Route::get('/telegram/csv/useractive',[TelegramController::class,'downloadUserActive']);
Route::post('/telegram/mac/binding',[TelegramController::class,'addMaccBinding']);
Route::get('/token',[TelegramController::class,'getCsrfToken'])->name('get.token');
Route::post('/telegram/csv/email',[TelegramController::class,'downloadEmail']);
Route::get('/traffic/get/{interface}',[RouterOsController::class,'fetchHtml'])->name('get.traffic');
/** user admin type */
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/register', function () { return view('user.register');})->name('register');
    Route::post('/register',[UserController::class,'store'])->name('postregister');
    Route::get('/getusers', [UserController::class,'getUsers'])->name('users');
    // Route::get('/users', function () { return view('user.users');});
    Route::get('/getusers', [UserController::class,'allUsers'])->name('get.admin.users');
    Route::get('/users', function () {return view('user.userv1');})->name('show.admin.users');
    Route::post('/user',[UserController::class,'storeNewUser'])->name('store.admin.user');
    Route::get('/user/{id}',[UserController::class,'getUserById'])->name('admin.user.by.id');
    Route::post('/user/{id}',[UserController::class,'userUpdate'])->name('update.admin.user');
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
    // Route::get('/user/profile',[RouterOsController::class,'getUserProfiles'])->name('get.user.profile');
    Route::get('/userprofile',[RouterOsController::class,'showUserProfile'])->name('show.user.profile');
    Route::post('/userprofile',[RouterOsController::class,'addUserProfile'])->name('post.user.profile');
    Route::post('/user/profile/{id}',[RouterOsController::class,'updateUserProfile']);
    Route::get('/profiles', function () { return view('routeros.userprofile'); })->name('user.profile');
    Route::get('/profiles/{id}', [RouterOsController::class,'getUserProfile']);
    Route::delete('/profiles/{id}', [RouterOsController::class,'deleteUserprofile']);
    Route::get('/telegram/get/users', [TelegramController::class,'getUsers'])->name('get.telegram.users');
    Route::get('/telegram/register/users', function() { return view('telegram.user');})->name('register.telegram.users');
    Route::post('/telegram/user/update',[TelegramController::class,'update'])->name('update.telegram.user');
    Route::get('/traffic/wan', [RouterOsController::class,'wanTraffic'])->name('wan.traffic');
    Route::get('/traffic/guest', [RouterOsController::class,'guestTraffic'])->name('guest.traffic');

});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout',[UserController::class,'logout'])->name('logout');
});
/** testing */
// Route::get('/testpage', function () { return view('test.tablev5');});
// Route::get('/testusers', function () { return view('user.users');});
// Route::get('/testguest',[GuestController::class,'displaydata']);
// Route::get('/testgetusers', [UserController::class,'getUsers']);
// Route::get('/table', function () { return view('test.table');});
// Route::get('/modal', function () { return view('test.modal');});
// Route::get('/active', [RouterOsController::class,'showActiveUser']);
// Route::get('/testreg', function () { return view('user.registerv1'); });
// Route::post('/fetch',[RouterOsController::class,'testPost'])->name('test.post');

// Route::get('/register', function () { return view('user.register');})->name('register');
// Route::post('/register',[UserController::class,'store'])->name('postregister');

// Route::get('/test/form', function () { return view('test.form');});


