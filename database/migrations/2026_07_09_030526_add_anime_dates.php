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
        if (! Schema::hasColumns('anime', ['start_date', 'end_date'])) {
            Schema::table('anime', function (Blueprint $table) {
                $table->char('start_date', 8)->nullable()->after('year');
                $table->char('end_date', 8)->nullable()->after('start_date');
                $table->text('mod_notes')->nullable()->after('synopsis');
            });
        }
    }
};
