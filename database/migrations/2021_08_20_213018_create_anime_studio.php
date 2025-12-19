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
        if (! Schema::hasTable('anime_studio')) {
            Schema::create('anime_studio', function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger('anime_id');
                $table->foreign('anime_id')->references('anime_id')->on('anime')->cascadeOnDelete();
                $table->unsignedBigInteger('studio_id');
                $table->foreign('studio_id')->references('studio_id')->on('studios')->cascadeOnDelete();
                $table->primary(['anime_id', 'studio_id']);
            });
        }
    }
};
