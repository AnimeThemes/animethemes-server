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
        if (! Schema::hasTable('studios')) {
            Schema::create('studios', function (Blueprint $table) {
                $table->id('studio_id');
                $table->timestamps(6);
                $table->softDeletes(precision: 6);
                $table->string('slug');
                $table->string('name');
            });
        }
    }
};
