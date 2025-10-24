<?php

declare(strict_types=1);

use App\Models\Wiki\Song;
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
        if (! Schema::hasColumn(Song::TABLE, Song::ATTRIBUTE_TITLE_NATIVE)) {
            Schema::table(Song::TABLE, function (Blueprint $table) {
                $table->string(Song::ATTRIBUTE_TITLE_NATIVE)->nullable()->after(Song::ATTRIBUTE_TITLE);
            });
        }
    }
};
