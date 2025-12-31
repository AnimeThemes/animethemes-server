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
        if (! Schema::hasColumns('announcements', ['start_at', 'end_at'])) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->timestamp('start_at', 6)->nullable();
                $table->timestamp('end_at', 6)->nullable();
            });
        }
    }
};
