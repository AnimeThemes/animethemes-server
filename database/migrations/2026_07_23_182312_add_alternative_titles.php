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
        if (! Schema::hasColumns('anime', ['title', 'title_english', 'title_native'])) {
            Schema::table('anime', function (Blueprint $table) {
                $table->renameColumn('name', 'title');
                $table->string('title_english')->after('title')->nullable();
                $table->string('title_native')->after('title_english')->nullable();
            });
        }

        if (! Schema::hasColumns('submission_anime', ['title', 'title_english', 'title_native'])) {
            Schema::table('submission_anime', function (Blueprint $table) {
                $table->renameColumn('name', 'title');
                $table->string('title_english')->after('title')->nullable();
                $table->string('title_native')->after('title_english')->nullable();
            });
        }

        if (! Schema::hasColumns('artists', ['name_native'])) {
            Schema::table('artists', function (Blueprint $table) {
                $table->string('name_native')->after('name')->nullable();
            });
        }

        if (! Schema::hasColumns('submission_artists', ['name_native'])) {
            Schema::table('submission_artists', function (Blueprint $table) {
                $table->string('name_native')->after('name')->nullable();
            });
        }

        if (! Schema::hasColumn('series', 'title')) {
            Schema::table('series', function (Blueprint $table) {
                $table->renameColumn('name', 'title');
            });
        }

        if (! Schema::hasColumn('submission_series', 'title')) {
            Schema::table('submission_series', function (Blueprint $table) {
                $table->renameColumn('name', 'title');
            });
        }

        if (! Schema::hasColumn('synonyms', 'language')) {
            Schema::table('synonyms', function (Blueprint $table) {
                $table->string('language')->after('text')->nullable();
            });
        }

        if (! Schema::hasColumn('submission_synonyms', 'language')) {
            Schema::table('submission_synonyms', function (Blueprint $table) {
                $table->string('language')->after('text')->nullable();
            });
        }
    }
};
