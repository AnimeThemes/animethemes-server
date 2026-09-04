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
        if (! Schema::hasColumn('themes', 'group_id')) {
            Schema::table('themes', function (Blueprint $table) {
                $table->unsignedBigInteger('group_id')->nullable();
                $table->foreign('group_id')->references('group_id')->on('groups')->nullOnDelete();
            });
        }
    }
};
