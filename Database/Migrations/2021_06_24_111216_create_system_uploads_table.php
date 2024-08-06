<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'system';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('上传信息表');
            $table->id()->comment('主键');
            $table->string('storage_mode', 10)->default('1')->comment('存储模式');
            $table->string('origin_name', 255)->nullable()->comment('原文件名');
            $table->string('object_name', 255)->nullable()->comment('新文件名');
            $table->string('hash', 64)->nullable()->comment('文件hash');
            $table->string('mime_type', 255)->nullable()->comment('资源类型');
            $table->string('storage_path', 100)->nullable()->comment('存储目录');
            $table->string('suffix', 10)->nullable()->comment('文件后缀');
            $table->bigInteger('size_byte')->nullable()->comment('字节数');
            $table->string('size_info', 50)->nullable()->comment('文件大小');
            $table->string('url', 255)->nullable()->comment('url地址');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->operators();
            $table->extJson();
            $table->timestamps();
            $table->softDeletes();

            $table->index('storage_path');
            $table->unique('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
