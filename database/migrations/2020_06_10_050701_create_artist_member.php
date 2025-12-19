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
        if (! Schema::hasTable('artist_member')) {
            Schema::create('artist_member', function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger('artist_id');
                $table->foreign('artist_id')->references('artist_id')->on('artists')->cascadeOnDelete();
                $table->unsignedBigInteger('member_id');
                $table->foreign('member_id')->references('artist_id')->on('artists')->cascadeOnDelete();
                $table->primary(['artist_id', 'member_id']);
                $table->string('as')->nullable();
                $table->string('alias')->nullable();
                $table->string('notes')->nullable();
            });
        }
    }
};
