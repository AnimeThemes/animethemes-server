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
        if (! Schema::hasTable('features')) {
            Schema::create('features', function (Blueprint $table) {
                $table->id('feature_id');
                $table->string('name');
                $table->string('scope');
                $table->text('value');
                $table->timestamps(6);

                $table->unique(['name', 'scope']);
            });
        }
    }
};
