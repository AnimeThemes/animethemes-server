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
        if (! Schema::hasTable('video_scripts')) {
            Schema::create('video_scripts', function (Blueprint $table) {
                $table->id('script_id');
                $table->timestamps(6);
                $table->softDeletes(precision: 6);
                $table->string('path');

                $table->unsignedBigInteger('video_id')->nullable();
                $table->foreign('video_id')->references('video_id')->on('videos')->nullOnDelete();
            });
        }
    }
};
