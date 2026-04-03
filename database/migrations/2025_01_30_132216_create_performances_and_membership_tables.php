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
        if (! Schema::hasTable('performances')) {
            Schema::create('performances', function (Blueprint $table) {
                $table->id('performance_id');

                $table->unsignedBigInteger('song_id');
                $table->foreign('song_id')->references('song_id')->on('songs')->cascadeOnDelete();

                $table->unsignedBigInteger('artist_id');
                $table->foreign('artist_id')->references('artist_id')->on('artists')->cascadeOnDelete();

                $table->unsignedBigInteger('member_id')->nullable();
                $table->foreign('member_id')->references('artist_id')->on('artists')->nullOnDelete();

                $table->string('alias')->nullable();
                $table->string('as')->nullable();
                $table->string('member_alias')->nullable();
                $table->string('member_as')->nullable();
                $table->timestamps(6);
                $table->softDeletes('deleted_at', 6);

                $table->unique(['song_id', 'artist_id', 'member_id', 'deleted_at'], 'unique_performance');
            });
        }
    }
};
