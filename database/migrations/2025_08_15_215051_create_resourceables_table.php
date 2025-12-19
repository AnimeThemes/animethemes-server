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
        if (! Schema::hasTable('resourceables')) {
            Schema::create('resourceables', function (Blueprint $table) {
                $table->unsignedBigInteger('resource_id');
                $table->foreign('resource_id')->references('resource_id')->on('resources')->cascadeOnDelete();

                $table->morphs('resourceable');
                $table->string('as')->nullable();

                $table->timestamps(6);

                $table->primary([
                    'resource_id',
                    'resourceable_type',
                    'resourceable_id',
                ]);
            });
        }
    }
};
