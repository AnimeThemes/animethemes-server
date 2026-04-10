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
        if (! Schema::hasTable('submissions')) {
            Schema::create('submissions', function (Blueprint $table) {
                $table->id();

                $table->string('type');
                $table->nullableUuidMorphs('actionable');

                $table->json('fields')->nullable();

                $table->longText('notes')->nullable();
                $table->longText('moderator_notes')->nullable();

                $table->integer('status')->nullable();

                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

                $table->unsignedBigInteger('moderator_id')->nullable();
                $table->foreign('moderator_id')->references('id')->on('users')->nullOnDelete();

                $table->boolean('locked')->default(false);

                $table->timestamp('finished_at', 6)->nullable();
                $table->timestamps(6);

                $table->index('status');
            });
        }
    }
};
