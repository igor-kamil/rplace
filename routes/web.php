<?php

use App\Events\ColorChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;


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

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('app');
    })->name('home');
    
    Route::get('/map', function () {
        $pixels = [];
        for ($x = 0; $x < config('settings.width'); $x++ ) {
            $rows = [];
            for ($y = 0; $y < config('settings.height'); $y++) {
                $rows[] = "{$x}:{$y}";
            }
            $pixels[] = Redis::mget($rows);
        }
        return $pixels;
    });

    Route::post('/save', function (Request $request) {
        
        Redis::set($request->key, $request->color . ':' . $request->user()->email);
        ColorChanged::dispatch([
            'key' => $request->key,
            'color' => $request->color . ':' . $request->user()->email,
        ]);
        return response()->json(Redis::get($request->key));
    });
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
