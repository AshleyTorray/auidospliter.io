<?php

use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudioFileController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/audio_spliter', [AudioFileController::class, 'audiospliter'])->middleware(['auth', 'verified'])->name('audiospliter');

Route::get('/audioManagement', [AudioFileController::class, 'index'])->middleware(['auth', 'verified'])->name('audio');
Route::get('/aduio.diplay/{filetype}', [AudioFileController::class, 'displayAudio'])->middleware(['auth', 'verified'])->name('audio.display');
Route::get('/excel/import/{filename}/{filepath}', [ExcelImportController::class, 'import'])->middleware(['auth', 'verified'])->name('excel.import');

Route::get('/import-audio', [AudioFileController::class, 'getAudioFromLocal'])->middleware(['auth', 'verified'])->name('import.audio');
Route::get('/download/{filePath}/{fileName}', function ($filePath, $fileName) {
    $file = $filePath . '/' . $fileName;

    if (file_exists($file)) {
        return Response::download($file, $fileName);
    }

    abort(404); 
})->name('download.file');
Route::middleware('auth')->group(function () { 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
