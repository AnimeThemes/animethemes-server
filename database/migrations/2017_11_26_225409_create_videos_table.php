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
        if (! Schema::hasTable('videos')) {
            Schema::create('videos', function (Blueprint $table) {
                $table->id('video_id');
                $table->timestamps(6);
                $table->softDeletes(precision: 6);
                $table->string('basename');
                $table->string('filename');
                $table->string('path');
                $table->integer('size');
                $table->string('mimetype');
                $table->integer('resolution')->nullable();
                $table->boolean('nc')->default(false);
                $table->boolean('subbed')->default(false);
                $table->boolean('lyrics')->default(false);
                $table->boolean('uncen')->default(false);
                $table->integer('overlap');
                $table->integer('source')->nullable();
            });
        }
    }
};
