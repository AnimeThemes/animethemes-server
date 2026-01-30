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
        if (! Schema::hasTable('anime_synonyms')) {
            Schema::create('anime_synonyms', function (Blueprint $table) {
                $table->id('synonym_id');
                $table->timestamps(6);
                $table->softDeletes(precision: 6);
                $table->string('text')->nullable();
                $table->integer('type');

                $table->unsignedBigInteger('anime_id');
                $table->foreign('anime_id')->references('anime_id')->on('anime')->cascadeOnDelete();
            });
        }
    }
};
