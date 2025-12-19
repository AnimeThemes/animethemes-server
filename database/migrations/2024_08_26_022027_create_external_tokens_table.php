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
        if (! Schema::hasTable('external_tokens')) {
            Schema::create('external_tokens', function (Blueprint $table) {
                $table->id('token_id');
                $table->longText('access_token')->nullable();
                $table->longText('refresh_token')->nullable();

                $table->unsignedBigInteger('profile_id')->nullable()->unique();
                $table->foreign('profile_id')->references('profile_id')->on('external_profiles')->cascadeOnDelete();

                $table->timestamps(6);
            });
        }
    }
};
