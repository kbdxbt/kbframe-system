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
        Schema::create('messages', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('系统消息表');
            $table->id()->comment('主键');
            $table->integer('recipient_id')->comment('接收者ID');
            $table->tinyInteger('type')->default(0)->comment('类型');
            $table->string('channel', 100)->default('')->comment('渠道');
            $table->string('message_id', 255)->comment('消息ID');
            $table->string('subject', 255)->default('')->comment('主题');
            $table->text('content')->comment('内容');
            $table->integer('priority')->default(0)->comment('优先级');
            $table->string('send_by', 255)->default('')->comment('发送人');
            $table->json('options')->nullable()->comment('附加');
            $table->tinyInteger('status')->default(0)->comment('状态[0:未发送;1:发送成功;2:发送失败;]');
            $table->tinyInteger('read_status')->default(0)->comment('阅读状态[0:未读;1:已读;]');
            $table->timestamp('readed_at')->nullable()->comment('读取时间');
            $table->timestamps();
            $table->softDeletes();

            $table->index('recipient_id', 'idx_recipient_id');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
