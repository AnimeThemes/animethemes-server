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
        if (! Schema::hasTable('external_profiles')) {
            Schema::create('external_profiles', function (Blueprint $table) {
                $table->timestamps(6);
                $table->id('profile_id');
                $table->string('name');
                $table->integer('site');
                $table->integer('visibility')->default(1);

                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

                $table->timestamp('synced_at', 6)->nullable();
                $table->integer('external_user_id')->nullable();
            });
        }
    }
};
