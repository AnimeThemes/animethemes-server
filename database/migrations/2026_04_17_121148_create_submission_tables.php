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

                $table->nullableMorphs('actionable');
                $table->nullableMorphs('submitted');

                $table->text('changes')->nullable();

                $table->longText('source')->nullable();
                $table->longText('notes')->nullable();

                $table->integer('status')->nullable();

                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

                $table->unsignedBigInteger('assignee_id')->nullable();
                $table->foreign('assignee_id')->references('id')->on('users')->nullOnDelete();

                $table->boolean('locked')->default(false);

                $table->timestamp('finished_at', 6)->nullable();
                $table->timestamps(6);

                $table->index('status');
            });
        }

        if (! Schema::hasTable('submissions_comparison')) {
            Schema::create('submissions_comparison', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('submission_id');
                $table->foreign('submission_id')->references('id')->on('submissions')->cascadeOnDelete();

                $table->nullableMorphs('actionable', 'actionable_index');
                $table->nullableMorphs('submitted', 'submitted_index');
                $table->nullableMorphs('submitted_pivot', 'submitted_pivot_index');
                $table->integer('action');
            });
        }

        if (! Schema::hasTable('submission_anime')) {
            Schema::create('submission_anime', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->string('slug');
                $table->string('name');
                $table->integer('year')->nullable();
                $table->integer('season')->nullable();
                $table->integer('format')->nullable();
                $table->text('synopsis')->nullable();
            });
        }

        if (! Schema::hasTable('submission_synonyms')) {
            Schema::create('submission_synonyms', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->morphs('synonymable');
                $table->string('text');
                $table->integer('type');
            });
        }

        if (! Schema::hasTable('submission_songs')) {
            Schema::create('submission_songs', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->string('title')->nullable();
                $table->string('title_native')->nullable();
            });
        }

        if (! Schema::hasTable('submission_artists')) {
            Schema::create('submission_artists', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->string('slug');
                $table->string('name');
                $table->text('information')->nullable();
            });
        }

        if (! Schema::hasTable('submission_artist_member')) {
            Schema::create('submission_artist_member', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->string('as')->nullable();
                $table->string('alias')->nullable();
                $table->string('notes')->nullable();
                $table->integer('relevance')->default(1)->nullable();
            });
        }

        if (! Schema::hasTable('submission_performances')) {
            Schema::create('submission_performances', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->unsignedBigInteger('song_id');
                $table->foreign('song_id')->references('id')->on('submission_songs')->cascadeOnDelete();

                $table->unsignedBigInteger('artist_id');
                $table->foreign('artist_id')->references('id')->on('submission_artists')->cascadeOnDelete();

                $table->unsignedBigInteger('member_id')->nullable();
                $table->foreign('member_id')->references('id')->on('submission_artists')->nullOnDelete();

                $table->string('alias')->nullable();
                $table->string('as')->nullable();
                $table->string('member_alias')->nullable();
                $table->string('member_as')->nullable();
                $table->integer('relevance')->default(1);
            });
        }

        if (! Schema::hasTable('submission_anime_themes')) {
            Schema::create('submission_anime_themes', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->integer('type');
                $table->integer('sequence')->nullable();
                $table->string('slug');

                $table->unsignedBigInteger('anime_id');
                $table->foreign('anime_id')->references('id')->on('submission_anime')->cascadeOnDelete();

                $table->unsignedBigInteger('song_id')->nullable();
                $table->foreign('song_id')->references('id')->on('submission_songs')->nullOnDelete();

                $table->unsignedBigInteger('group_id')->nullable();
                $table->foreign('group_id')->references('group_id')->on('groups')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('submission_anime_theme_entries')) {
            Schema::create('submission_anime_theme_entries', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->integer('version');
                $table->string('episodes')->nullable();
                $table->boolean('nsfw')->default(false);
                $table->boolean('spoiler')->default(false);
                $table->text('notes')->nullable();

                $table->unsignedBigInteger('theme_id');
                $table->foreign('theme_id')->references('id')->on('submission_anime_themes')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('submission_resources')) {
            Schema::create('submission_resources', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->integer('site');
                $table->string('link');
                $table->integer('external_id')->nullable();
            });
        }

        if (! Schema::hasTable('submission_series')) {
            Schema::create('submission_series', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->string('slug');
                $table->string('name');
            });
        }

        if (! Schema::hasTable('submission_studios')) {
            Schema::create('submission_studios', function (Blueprint $table) {
                $table->id();
                $table->timestamps(6);
                $table->string('slug');
                $table->string('name');
            });
        }
    }
};
