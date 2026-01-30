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
        if (! Schema::hasTable('images')) {
            Schema::create('images', function (Blueprint $table) {
                $table->id('image_id');
                $table->timestamps(6);
                $table->softDeletes(precision: 6);
                $table->string('path');
                $table->integer('facet');
            });
        }
    }
};
