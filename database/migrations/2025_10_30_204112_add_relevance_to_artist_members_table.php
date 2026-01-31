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
        if (! Schema::hasColumn('artist_member', 'relevance')) {
            Schema::table('artist_member', function (Blueprint $table) {
                $table->integer('relevance')->default(1)->nullable();
            });
        }
    }
};
