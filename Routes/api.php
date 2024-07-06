<?php

use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\TaskController;
use Modules\System\Http\Controllers\UploadController;

Route::prefix('v1')->middleware(['api'])->group(function () {

    Route::group(['prefix' => 'system'], function () {
        // 上传模块
        Route::prefix('upload')
            ->controller(UploadController::class)
            ->name('upload.')
            ->group(function () {
                Route::post('image', 'uploadImage')->name('image');
                Route::post('file', 'uploadFile')->name('uploadFile');
                Route::post('rich', 'uploadRich')->name('uploadRich');
            });

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
