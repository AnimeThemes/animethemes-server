<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /** @var array<string, string> $tableNames */
        $tableNames = Config::array('prohibition.table_names');

        Schema::create($tableNames['prohibition'], function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();

            $table->unique('name');
        });

        Schema::create($tableNames['sanction'], function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();

            $table->unique('name');
        });

        Schema::create($tableNames['sanction_prohibition'], function (Blueprint $table) use ($tableNames): void {
            $table->unsignedBigInteger('sanction_id');
            $table->foreign('sanction_id')->references('id')->on($tableNames['sanction'])->cascadeOnDelete();

            $table->unsignedBigInteger('prohibition_id');
            $table->foreign('prohibition_id')->references('id')->on($tableNames['prohibition'])->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['sanction_id', 'prohibition_id']);
        });

        Schema::create($tableNames['model_sanctions'], function (Blueprint $table) use ($tableNames): void {
            $table->morphs('model');

            $table->unsignedBigInteger('sanction_id');
            $table->foreign('sanction_id')->references('id')->on($tableNames['sanction'])->cascadeOnDelete();

            $table->nullableMorphs('moderator');
            $table->timestamp('expires_at')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->primary(['model_type', 'model_id', 'sanction_id']);
            $table->index('expires_at');
        });

        Schema::create($tableNames['model_prohibitions'], function (Blueprint $table) use ($tableNames): void {
            $table->morphs('model');

            $table->unsignedBigInteger('prohibition_id');
            $table->foreign('prohibition_id')->references('id')->on($tableNames['prohibition'])->cascadeOnDelete();

            $table->nullableMorphs('moderator');
            $table->timestamp('expires_at')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->primary(['model_type', 'model_id', 'prohibition_id']);
            $table->index('expires_at');
        });
    }
};
