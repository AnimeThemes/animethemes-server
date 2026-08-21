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
        if (! Schema::hasTable('resources')) {
            Schema::create('resources', function (Blueprint $table) {
                $table->id('resource_id');
                $table->timestamp('created_at', 6)->useCurrent();
                $table->timestamp('updated_at', 6)->useCurrent();
                $table->softDeletes(precision: 6);
                $table->integer('site');
                $table->string('link');
                $table->integer('external_id')->nullable();
            });
        }
    }
};
