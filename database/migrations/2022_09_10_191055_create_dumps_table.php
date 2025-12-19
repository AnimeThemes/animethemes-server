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
        if (! Schema::hasTable('dumps')) {
            Schema::create('dumps', function (Blueprint $table) {
                $table->id('dump_id');
                $table->timestamps(6);
                $table->string('path');
            });
        }
    }
};
