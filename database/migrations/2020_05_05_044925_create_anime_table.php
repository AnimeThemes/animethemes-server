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
                $table->timestamp('created_at', 6)->useCurrent();
                $table->timestamp('updated_at', 6)->useCurrent();
                $table->softDeletes(precision: 6);
                $table->string('slug');
                $table->string('name');
                $table->integer('year')->nullable();
                $table->integer('season')->nullable();
                $table->integer('format')->nullable();
                $table->text('synopsis')->nullable();
            });
        }
    }
};
