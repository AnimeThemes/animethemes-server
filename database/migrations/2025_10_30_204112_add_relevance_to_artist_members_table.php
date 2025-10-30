<?php

declare(strict_types=1);

use App\Pivots\Wiki\ArtistMember;
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
        if (! Schema::hasColumn(ArtistMember::TABLE, ArtistMember::ATTRIBUTE_RELEVANCE)) {
            Schema::table(ArtistMember::TABLE, function (Blueprint $table) {
                $table->integer(ArtistMember::ATTRIBUTE_RELEVANCE)->nullable();
            });
        }
    }
};
