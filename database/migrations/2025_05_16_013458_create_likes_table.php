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
        if (! Schema::hasTable('likes')) {
            Schema::create('likes', function (Blueprint $table) {
                $table->id('like_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

                $table->morphs('likeable');
                $table->timestamps(6);

                $table->index(['user_id', 'likeable_type', 'likeable_id']);
            });
        }
    }
};
