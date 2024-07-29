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
        Schema::create('http_logs', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('Http日志表');
            $table->id()->comment('主键');
            $table->string('ip', 16)->comment('IP')->nullable();
            $table->string('url', 128)->comment('URL')->nullable();
            $table->string('method', 10)->comment('Method')->nullable();
            $table->string('request_id', 50)->comment('请求id')->nullable();
            $table->mediumText('request_params')->comment('请求参数');
            $table->mediumText('request_header')->comment('请求header');
            $table->string('request_time', 20)->comment('请求时间')->nullable();
            $table->string('response_code', 10)->comment('响应状态码')->nullable();
            $table->mediumText('response_header')->comment('响应header');
            $table->mediumText('response_body')->comment('响应body');
            $table->string('response_time', 20)->comment('响应时间')->nullable();
            $table->string('duration', 10)->comment('请求时长')->nullable();
            $table->text('curl_text')->comment('curl文本')->nullable();
            $table->string('device', 50)->comment('设备')->nullable();
            $table->string('version', 20)->comment('app版本')->nullable();
            $table->json('ext');
            $table->timestamps();

            $table->index('ip');
            $table->index('url');
            $table->index('request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('http_logs');
    }
};
