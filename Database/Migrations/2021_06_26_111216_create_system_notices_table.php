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
        Schema::create('notices', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('系统公告表');
            $table->id()->comment('主键');
            $table->string('category', 100)->comment('类别');
            $table->string('title', 255)->default('')->comment('公告标题');
            $table->text('content')->comment('公告内容');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->tinyInteger('is_top')->default(0)->comment('是否置顶');
            $table->integer('click_num')->default(0)->comment('浏览次数');
            $table->integer('sort')->default(0)->comment('排序');
            $table->operators();
            $table->extJson();
            $table->timestamp('scheduled_at')->nullable()->comment('预约时间');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'title'], 'idx_task');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
