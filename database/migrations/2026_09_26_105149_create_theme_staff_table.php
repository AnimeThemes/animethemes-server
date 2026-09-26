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
        if (! Schema::hasTable('theme_staff')) {
            Schema::create('theme_staff', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('theme_id');
                $table->foreign('theme_id')->references('theme_id')->on('themes')->cascadeOnDelete();
                $table->unsignedBigInteger('artist_id');
                $table->foreign('artist_id')->references('artist_id')->on('artists')->cascadeOnDelete();

                $table->string('role');
                $table->string('alias')->nullable();
                $table->integer('relevance')->default(1);

                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes('deleted_at');

                $table->unique(['theme_id', 'artist_id', 'role', 'deleted_at'], 'unique_theme_staff');
            });
        }
    }
};
