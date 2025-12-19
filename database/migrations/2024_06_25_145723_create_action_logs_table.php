<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('action_logs')) {
            Schema::create('action_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('batch_id');

                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

                $table->string('name');
                $table->morphs('actionable');
                $table->morphs('target');
                $table->string('model_type');
                $table->uuid('model_id')->nullable();
                $table->integer('status')->nullable();
                $table->json('fields')->nullable();
                $table->text('exception')->nullable();
                $table->timestamps(6);
                $table->timestamp('finished_at', 6)->nullable();

                $table->index(['batch_id', 'model_type', 'model_id']);
            });
        }
    }
};
