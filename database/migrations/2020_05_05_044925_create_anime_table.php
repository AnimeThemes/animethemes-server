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
        if (! Schema::hasTable('anime')) {
            Schema::create('anime', function (Blueprint $table) {
                $table->id('anime_id');
                $table->timestamps(6);
                $table->softDeletes(precision: 6);
                $table->string('slug');
                $table->string('name');
                $table->integer('year')->nullable();
                $table->integer('season')->nullable();
                $table->integer('media_format')->default(0);
                $table->text('synopsis')->nullable();
            });
        }
    }
};
