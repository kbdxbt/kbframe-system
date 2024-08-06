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
        Schema::create('configs', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('系统公告表');
            $table->id()->comment('主键');
            $table->string('title', 32)->nullable()->comment('配置标题');
            $table->string('name', 32)->nullable()->comment('配置名称');
            $table->text('value')->comment('配置值');
            $table->boolean('group')->default(0)->comment('配置分组');
            $table->string('type', 16)->nullable()->comment('配置类型');
            $table->string('options')->nullable()->comment('配置额外值');
            $table->string('tip', 100)->nullable()->comment('配置说明');
            $table->boolean('sort')->default(0)->comment('排序');
            $table->status()->comment(\Modules\Core\Enums\StatusEnum::allToDatabaseNote('状态'));
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
