<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('activity_log')) {
            Schema::create('activity_log', function (Blueprint $table) {
                $table->id();
                $table->string('log_name')->nullable()->index();
                $table->text('description');
                $table->nullableMorphs('subject', 'subject');
                $table->nullableMorphs('related', 'related');
                $table->string('event')->nullable();
                $table->nullableMorphs('causer', 'causer');
                $table->json('attribute_changes')->nullable();
                $table->json('properties')->nullable();
                $table->integer('status')->nullable();
                $table->text('exception')->nullable();
                $table->timestamps(6);
                $table->timestamp('finished_at', 6)->nullable();
            });
        }
    }
};
