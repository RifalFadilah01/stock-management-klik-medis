<?php

use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('medicines.index');
});

// Medicine Routes
Route::resource('medicines', MedicineController::class);

// Export Routes
Route::post('exports/excel', [ExportController::class, 'excel'])->name('export.excel');
Route::get('exports/progress/{exportId}', [ExportController::class, 'progress'])->name('export.progress');
Route::get('exports/download/{exportId}', [ExportController::class, 'download'])->name('export.download');
Route::post('exports/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
