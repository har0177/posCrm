<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('admin/login');
})->name('login');

Route::get('/test', function () {
  
  dd(extension_loaded('sqlite3'), extension_loaded('pdo_sqlite'));
  // Retrieve and format your permissions as needed
  $permissions = config( 'permissions' );
  
  return collect($permissions)->flatMap(function ($data, $key) {
    return collect($data)->map(function ($value, $key) {
      return $value;
    });
  })->toArray();
  
  
});


