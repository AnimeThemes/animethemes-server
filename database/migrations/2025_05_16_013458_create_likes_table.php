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
        if (! Schema::hasTable('favorites')) {
            Schema::create('favorites', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

                $table->morphs('favoriteable');
                $table->timestamp('created_at', 6)->useCurrent();

                $table->index(['user_id', 'favoriteable_type', 'favoriteable_id']);
            });
        }
    }
};
