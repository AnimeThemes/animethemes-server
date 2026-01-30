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
        if (! Schema::hasTable('imageables')) {
            Schema::create('imageables', function (Blueprint $table) {
                $table->unsignedBigInteger('image_id');
                $table->foreign('image_id')->references('image_id')->on('images')->cascadeOnDelete();

                $table->morphs('imageable');
                $table->integer('depth');

                $table->timestamps(6);

                $table->primary([
                    'image_id',
                    'imageable_type',
                    'imageable_id',
                ]);
            });
        }
    }
};
