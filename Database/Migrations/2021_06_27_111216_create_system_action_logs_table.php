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
        Schema::create('action_logs', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('系统公告表');
            $table->id()->comment('主键');
            $table->string('log_id', 100)->default('');
            $table->bigInteger('model_id')->default(0);
            $table->string('key', 32)->default('');
            $table->string('subject', 32)->default('');
            $table->string('description', 500)->default('');
            $table->json('options')->nullable();
            $table->operators();
            $table->timestamps();
            $table->softDeletes();

            $table->index('model_id', 'idx_model_id');
            $table->index('key', 'idx_key');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};
