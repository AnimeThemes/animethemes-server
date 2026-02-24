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
        if (! Schema::hasTable('page_roles')) {
            Schema::create('page_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('page_id');
                $table->foreign('page_id')->references('page_id')->on('pages')->cascadeOnDelete();
                $table->unsignedBigInteger('role_id');
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->integer('type');
                $table->timestamps(6);
            });
        }
    }
};
