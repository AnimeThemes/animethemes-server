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
        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id('announcement_id');
                $table->timestamp('created_at', 6)->useCurrent();
                $table->timestamp('updated_at', 6)->useCurrent();
                $table->text('content');
            });
        }
    }
};
