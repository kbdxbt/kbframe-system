<?php

use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\TaskController;

Route::prefix('v1')->middleware(['api'])->group(function () {

    Route::group(['prefix' => 'system'], function () {
        // 任务模块
        Route::prefix('task')
            ->controller(TaskController::class)
            ->name('task.')
            ->middleware(['auth:member'])->group(function () {
                Route::get('export', 'export')->name('export');
                Route::post('import', 'import')->name('import');
                Route::get('import_template', 'importTemplate')->name('import_template');
                Route::get('download_file', 'downloadFile')->name('download_file');
            });
    });

});
