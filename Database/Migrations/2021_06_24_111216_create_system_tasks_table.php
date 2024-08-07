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
        Schema::create('tasks', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('系统任务表');
            $table->id()->comment('主键');
            $table->integer('member_id')->comment('会员ID');
            $table->tinyInteger('type')->comment('类型');
            $table->string('source', 100)->default('')->comment('来源');
            $table->integer('num_rows')->default(0)->comment('行数');
            $table->bigInteger('upload_id')->default(0)->comment('上传文件ID');
            $table->decimal('progress', 5, 2)->default(0.00)->comment('进度');
            $table->string('fail_reason', 500)->default('')->comment('失败原因');
            $table->string('error_reason', 2000)->default('')->comment('错误原因');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->integer('created_by')->default(0)->comment('创建用户ID');
            $table->extJson();
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamp('scheduled_at')->nullable()->comment('预约时间');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['member_id', 'type', 'source'], 'idx_task');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
