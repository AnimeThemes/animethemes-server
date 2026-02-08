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
        if (! Schema::hasColumns('pages', ['previous_id', 'next_id'])) {
            Schema::table('pages', function (Blueprint $table) {
                $table->unsignedBigInteger('previous_id')->nullable();
                $table->foreign('previous_id')->references('page_id')->on('pages')->nullOnDelete();

                $table->unsignedBigInteger('next_id')->nullable();
                $table->foreign('next_id')->references('page_id')->on('pages')->nullOnDelete();
            });
        }
    }
};
