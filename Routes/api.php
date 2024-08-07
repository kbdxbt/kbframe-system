<?php

use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\ConfigController;
use Modules\System\Http\Controllers\HttpLogController;
use Modules\System\Http\Controllers\MessageController;
use Modules\System\Http\Controllers\NoticeController;
use Modules\System\Http\Controllers\TaskController;
use Modules\System\Http\Controllers\UploadController;

Route::prefix('v1')->middleware(['api'])->group(function () {

    Route::group(['prefix' => 'system', 'middleware' => 'auth:member'], function () {
        // 上传模块
        Route::prefix('upload')
            ->controller(UploadController::class)
            ->name('upload.')
            ->withoutMiddleware(['auth:member'])
            ->group(function () {
                Route::post('image', 'uploadImage')->name('image');
                Route::post('file', 'uploadFile')->name('uploadFile');
                Route::post('rich', 'uploadRich')->name('uploadRich');
            });

        // 任务模块
        Route::prefix('task')
            ->controller(TaskController::class)
            ->name('task.')
            ->group(function () {
                Route::get('export', 'export')->name('export');
                Route::post('import', 'import')->name('import');
                Route::get('import_template', 'importTemplate')->name('import_template');
                Route::get('download_file', 'downloadFile')->name('download_file');
            });

        // 公告模块
        Route::prefix('notice')
            ->controller(NoticeController::class)
            ->name('notice.')
            ->group(function () {
                Route::get('list', 'list')->name('list');
                Route::post('save', 'save')->name('save');
                Route::get('detail', 'detail')->name('detail');
                Route::post('delete', 'delete')->name('delete');
            });

        // 消息模块
        Route::prefix('message')
            ->controller(MessageController::class)
            ->name('message.')
            ->group(function () {
                Route::get('unread_list', 'unreadList')->name('unread');
                Route::post('mark_read', 'markRead')->name('mark_read');
                Route::get('detail', 'detail')->name('detail');
                Route::post('delete', 'delete')->name('delete');
            });

        // http日志模块
        Route::prefix('http_log')
            ->controller(HttpLogController::class)
            ->name('http_log.')
            ->middleware(['auth:member'])->group(function () {
                Route::get('list', 'list')->name('list');
            });

        // 配置模块
        Route::prefix('config')
            ->controller(ConfigController::class)
            ->name('config.')
            ->middleware(['auth:member'])->group(function () {
                Route::get('list', 'list')->name('list');
                Route::post('save', 'save')->name('save');
                Route::get('detail', 'detail')->name('detail');
                Route::post('delete', 'delete')->name('delete');
            });
    });

});
