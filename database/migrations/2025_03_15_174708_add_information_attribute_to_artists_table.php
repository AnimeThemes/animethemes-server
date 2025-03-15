<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
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
        if (! Schema::hasColumn(Artist::TABLE, Artist::ATTRIBUTE_INFORMATION)) {
            Schema::table(Artist::TABLE, function (Blueprint $table) {
                $table->text(Artist::ATTRIBUTE_INFORMATION)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(Artist::TABLE, Artist::ATTRIBUTE_INFORMATION)) {
            Schema::table(Artist::TABLE, function (Blueprint $table) {
                $table->dropColumn(Artist::ATTRIBUTE_INFORMATION);
            });
        }
    }
};
