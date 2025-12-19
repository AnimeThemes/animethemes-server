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
        if (! Schema::hasTable('submission_virtuals')) {
            Schema::create('submission_virtuals', function (Blueprint $table) {
                $table->id('submission_virtual_id');

                $table->boolean('exists')->default(false);
                $table->string('model_type');
                $table->json('fields');

                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

                $table->timestamps(6);
            });
        }
    }
};
