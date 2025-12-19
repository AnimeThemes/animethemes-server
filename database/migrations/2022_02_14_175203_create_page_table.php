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
        if (! Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id('page_id');
                $table->timestamps(6);
                $table->softDeletes(precision: 6);
                $table->string('slug');
                $table->string('name');
                $table->mediumText('body');
            });
        }
    }
};
