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
        if (! Schema::hasColumn('dumps', 'public')) {
            Schema::table('dumps', function (Blueprint $table) {
                $table->boolean('public')->default(false);
            });
        }
    }
};
