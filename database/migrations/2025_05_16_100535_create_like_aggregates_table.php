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
        if (! Schema::hasTable('like_aggregates')) {
            Schema::create('like_aggregates', function (Blueprint $table) {
                $table->morphs('likeable');
                $table->integer('value')->default(0);
                $table->primary(['likeable_id', 'likeable_type']);
            });
        }
    }
};
