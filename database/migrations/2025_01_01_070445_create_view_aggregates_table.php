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
        if (! Schema::hasTable('view_aggregates')) {
            Schema::create('view_aggregates', function (Blueprint $table) {
                $table->morphs('viewable');
                $table->integer('value')->default(0);
                $table->primary(['viewable_id', 'viewable_type']);
            });
        }
    }
};
