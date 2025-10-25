<?php

declare(strict_types=1);

use App\Models\Wiki\Song\Performance;
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
        if (! Schema::hasColumn(Performance::TABLE, Performance::ATTRIBUTE_RELEVANCE)) {
            Schema::table(Performance::TABLE, function (Blueprint $table) {
                $table->integer(Performance::ATTRIBUTE_RELEVANCE)->nullable()->after(Performance::ATTRIBUTE_AS);
            });
        }
    }
};
